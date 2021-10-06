<?php

namespace App\WampServer\Security\ClientAuthenticator;


use Thruway\Authentication\ClientAuthenticationInterface;
use Thruway\Message\AuthenticateMessage;
use Thruway\Message\ChallengeMessage;

class ServiceClientAuthenticator implements ClientAuthenticationInterface
{
    private string $secretToken;

    public function __construct(string $secretToken)
    {
        $this->secretToken = $secretToken;
    }

    public function getAuthId()
    {
        // TODO: Implement getAuthId() method.
    }

    public function setAuthId($authid)
    {
        // TODO: Implement setAuthId() method.
    }

    public function getAuthMethods(): array
    {
        return ['service'];
    }

    public function getAuthenticateFromChallenge(ChallengeMessage $msg): AuthenticateMessage
    {
        return new AuthenticateMessage($this->secretToken);
    }
}
