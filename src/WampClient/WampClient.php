<?php

namespace App\WampClient;

use Psr\Log\LoggerInterface;
use React\Promise\PromiseInterface;
use Thruway\Peer\Client;

class WampClient extends Client
{
    protected LoggerInterface $logger;

    /** @required */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function register(string $topicName, array $callable): void
    {
        $this->session->register($topicName, function ($arguments) use ($callable) {
            try {
                $reflectionMethod = new \ReflectionMethod($callable[0], $callable[1]);
                $parameters = $reflectionMethod->getParameters();
                if (count($parameters) !== count($arguments)) {
                    throw new \RuntimeException('Invalid number of arguments');
                }
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        });
    }

    public function subscribe(string $topicName, array $callable): void
    {
        $this->session->subscribe($topicName, function ($arguments, $payload) use ($callable) {
            try {
                $callable($arguments, $payload);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        });
    }

    public function publish($topicName, array $arguments = [], array $payload = [], array $options = []): PromiseInterface
    {
        $options['acknowledge'] = true;
        return $this->getSession()->publish($topicName, $arguments, $payload, $options);
    }
}
