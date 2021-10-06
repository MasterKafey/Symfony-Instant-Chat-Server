<?php

namespace App\WampServer\Security\AuthProviderClient;

use Exception;
use Thruway\Authentication\AbstractAuthProviderClient;
use Thruway\Message\HelloMessage;
use Thruway\Message\Message;

abstract class AbstractWampCraAuthProviderClient extends AbstractAuthProviderClient
{
    abstract protected function getKeyFromAuthId(string $authId): ?string;

    abstract protected function getRoleFromAuthId(string $authId): ?string;

    public function getMethodName(): string
    {
        return 'wampcra';
    }

    public function processHello(array $args): array
    {
        $helloMessage = array_shift($args);
        $sessionInfo = array_shift($args);

        if (!is_array($helloMessage) || !is_object($sessionInfo)) {
            return ['ERROR'];
        }

        try {
            $helloMessage = Message::createMessageFromArray($helloMessage);
        } catch (Exception $exception) {
            return ['ERROR'];
        }

        if (!$helloMessage instanceof HelloMessage
            || !$sessionInfo
            || !isset($sessionInfo->sessionId)
            || !isset($helloMessage->getDetails()->authid)
        ) {
            return ['ERROR'];
        }

        $authId = $helloMessage->getDetails()->authid;

        $challenge = [
            'authid' => $authId,
            'authmethod' => $this->getMethodName(),
            'nonce' => bin2hex(openssl_random_pseudo_bytes(22)),
            'timestamp' => date('Y-m-d\TH:i:sO'),
            'session' => $sessionInfo->sessionId,
        ];

        $serializeChallenge = json_encode($challenge);

        $challengeDetails = [
            'challenge' => $serializeChallenge,
            'challenge_method' => $this->getMethodName(),
        ];

        return ['CHALLENGE', $challengeDetails];
    }

    public function getChallengeFromExtra($extra)
    {
        return (is_object($extra)
        && isset($extra->challenge_details)
        && is_object($extra->challenge_details)
        && isset ($extra->challenge_details->challenge)
            ? json_decode($extra->challenge_details->challenge)
            : false
        );
    }
}
