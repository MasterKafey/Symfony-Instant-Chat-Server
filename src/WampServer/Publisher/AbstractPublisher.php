<?php

namespace App\WampServer\Publisher;


use React\Promise\PromiseInterface;

class AbstractPublisher
{
    protected Publisher $publisher;

    /** @required */
    public function setPublisher(Publisher $publisher): self
    {
        $this->publisher = $publisher;

        return $this;
    }

    public function publish($realms, string $topic, array $payload = [], array $parameters = [], array $options = ['acknowledge' => true]): PromiseInterface
    {
        return $this->publisher->publish($realms, $topic, $payload, $parameters, $options);
    }
}
