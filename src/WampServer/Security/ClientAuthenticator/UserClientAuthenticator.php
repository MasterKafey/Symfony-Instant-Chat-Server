<?php

namespace App\WampServer\Security\ClientAuthenticator;

use Thruway\Authentication\ClientAuthenticationInterface;
use Thruway\Message\AuthenticateMessage;
use Thruway\Message\ChallengeMessage;

class UserClientAuthenticator implements ClientAuthenticationInterface
{
    private string $id;

    public function getAuthId()
    {
        return $this->id;
    }

    public function setAuthId($authid)
    {
        $this->id = $authid;
    }

    public function getAuthMethods()
    {
        return ['user_wampcra'];
    }

    public function getAuthenticateFromChallenge(ChallengeMessage $msg): AuthenticateMessage
    {
        return new AuthenticateMessage($this->id);
    }
}
