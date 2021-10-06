<?php

namespace App\WampServer;

use Thruway\Transport\RatchetTransportProvider;

class RouterTransportProvider extends RatchetTransportProvider
{
    protected string $address;

    protected int $port;

    public function __construct(string $wampURL)
    {
        $configuration = parse_url($wampURL);

        if (!is_array($configuration)) {
            throw new \RuntimeException();
        }
        $address = $configuration['host'];
        $port = $configuration['port'];

        parent::__construct($address, $port);

        $this
            ->setAddress($address)
            ->setPort($port);
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function setPort(string $port): self
    {
        $this->port = $port;

        return $this;
    }
}
