<?php

namespace App\WampServer;

use React\Promise\Deferred;

class Message
{
    protected string $realm;
    protected string $topic;
    protected array $payload;
    protected array $options;
    protected Deferred $deferred;

    public function __construct(string $realm, string $topic, array $payload, Deferred $deferred, array $options)
    {
        $this->realm = $realm;
        $this->topic = $topic;
        $this->payload = $payload;
        $this->deferred = $deferred;
        $this->options = $options;
    }

    public function getRealm(): string
    {
        return $this->realm;
    }

    public function setRealm(string $realm): self
    {
        $this->realm = $realm;
        return $this;
    }

    public function getTopic(): string
    {
        return $this->topic;
    }

    public function setTopic(string $topic): self
    {
        $this->topic = $topic;
        return $this;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): self
    {
        $this->payload = $payload;
        return $this;
    }

    public function getDeferred(): Deferred
    {
        return $this->deferred;
    }

    public function setDeferred(Deferred $deferred): self
    {
        $this->deferred = $deferred;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}
