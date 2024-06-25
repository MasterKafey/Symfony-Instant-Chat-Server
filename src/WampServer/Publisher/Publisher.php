<?php

namespace App\WampServer\Publisher;

use App\WampClient\WampClient;
use App\WampServer\Message;
use App\WampServer\Security\ClientAuthenticator\ServiceClientAuthenticator;
use App\WampServer\WampTopic;
use React\EventLoop\Loop;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Thruway\ClientSession;
use Thruway\Transport\PawlTransportProvider;
use function React\Promise\all;

class Publisher
{
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_OPEN = 'open';

    protected array $wampClients = [];
    protected array $messages = [];
    protected array $statuses = [];

    protected string $wampURL;

    protected WampTopic $wampTopic;
    protected ServiceClientAuthenticator $serviceClientAuthenticator;

    public function __construct(
        string $wampURL,
        WampTopic $wampTopic,
        ServiceClientAuthenticator $serviceClientAuthenticator,
    )
    {
        $this->wampURL = $wampURL;
        $this->wampTopic = $wampTopic;
        $this->serviceClientAuthenticator = $serviceClientAuthenticator;
    }

    public function publish($realms, string $topic, array $payload = [], array $parameters = [], array $options = []): PromiseInterface
    {
        $topic = $this->wampTopic->getTopic($topic, $parameters);

        if (count($payload)) {
            $payload = (new ObjectNormalizer())->normalize($payload);
        }

        if (!is_array($realms)) {
            $realms = [$realms];
        }

        $loop = Loop::get();
        $promises = [];
        foreach ($realms as $realm) {
            $promises[] = $this->addMessage($realm, $topic, $payload, $options)->then(function () use ($loop) {
                $hasMessage = false;
                foreach ($this->messages as $message) {
                    if (count($message)) {
                        $hasMessage = true;
                        break;
                    }
                }

                if (!$hasMessage) {
                    $loop->stop();
                }
            });
        }

        $loop->run();

        gc_collect_cycles();

        return all($promises);
    }

    protected function getWampClient(string $realm): WampClient
    {
        if (!isset($this->wampClients[$realm])) {
            $this->createClient($realm);
        }

        return $this->wampClients[$realm];
    }

    protected function createClient(string $realm): void
    {
        $wampClient = new WampClient($realm, Loop::get());

        $transportProvider = new PawlTransportProvider($this->wampURL);
        $wampClient->addTransportProvider($transportProvider);
        $wampClient->addClientAuthenticator($this->serviceClientAuthenticator);

        $this->wampClients[$realm] = $wampClient;
        $this->statuses[$realm] = self::STATUS_CLOSED;

        $wampClient->on('open', function (ClientSession $session) use ($realm) {
            $this->statuses[$realm] = self::STATUS_OPEN;
            $this->flushMessages($realm);
        });

        $wampClient->on('close', function (ClientSession $session) use ($realm) {
            $this->statuses[$realm] = self::STATUS_CLOSED;
        });

        $wampClient->start(false);
    }

    protected function addMessage(string $realm, $topic, $payload, array $options = []): PromiseInterface
    {
        if (!isset($this->statuses[$realm]) || self::STATUS_OPEN !== $this->statuses[$realm]) {
            $id = $this->generateUniqueId();
            $deferred = new Deferred();
            if (!isset($this->messages[$realm])) {
                $this->messages[$realm] = [];
            }
            $this->messages[$realm][$id] = new Message($realm, $topic, $payload, $deferred, $options);

            // Force to instantiate the WampClient if it doesn't exist yet
            $this->getWampClient($realm);
            return $deferred->promise();
        } else {
            return $this->performRealPublish($realm, $topic, $payload, $options);
        }
    }

    public function flushMessages(string $realm): void
    {
        foreach ($this->messages[$realm] as $id => $message) {
            $this->performRealPublish(
                $message->getRealm(),
                $message->getTopic(),
                $message->getPayload()
            )->then(function ($value) use ($realm, $message, $id) {
                unset($this->messages[$realm][$id]);
                $message->getDeferred()->resolve($value);
            });
        }
    }

    protected function performRealPublish(string $realm, string $topic, array $payload = [], array $options = []): PromiseInterface
    {
        $wampClient = $this->getWampClient($realm);

        return $wampClient->publish(
            $topic,
            [],
            $payload,
            $options
        );
    }

    protected function generateUniqueId(): string
    {
        return uniqid('request');
    }
}
