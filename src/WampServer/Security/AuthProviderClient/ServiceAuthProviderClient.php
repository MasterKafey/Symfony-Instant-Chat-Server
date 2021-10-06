<?php

namespace App\WampServer\Security\AuthProviderClient;

use React\EventLoop\Loop;
use Thruway\Authentication\AbstractAuthProviderClient;

class ServiceAuthProviderClient extends AbstractAuthProviderClient
{
    protected string $secretToken;

    public function __construct(string $secretToken)
    {
        parent::__construct(['*'], Loop::get());

        $this->secretToken = $secretToken;
    }

    public function getMethodName(): string
    {
        return 'service';
    }

    public function processAuthenticate($signature, $extra = null): array
    {
        if ($signature === $this->secretToken) {
            $details = [
                'authroles' => ['internal'],
            ];

            return ['SUCCESS', $details];
        } else {
            return ['FAILURE'];
        }
    }
}
