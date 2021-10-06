<?php

namespace App\WampServer\Security\AuthProviderClient;


use App\Entity\User;
use App\WampServer\WampRouter;
use Doctrine\ORM\EntityManagerInterface;
use React\EventLoop\LoopInterface;

class UserAuthProviderClient extends AbstractWampCraAuthProviderClient
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, LoopInterface $loop = null)
    {
        parent::__construct([WampRouter::REALM_USER], $loop);
        $this->entityManager = $entityManager;
    }

    public function getMethodName(): string
    {
        return 'user_wampcra';
    }

    protected function getKeyFromAuthId(string $authId): ?string
    {
        $user = $this->entityManager->getRepository(User::class)->findByAuthId($authId);

        return null === $user ? null : $user->getId();
    }

    public function getRoleFromAuthId(string $authId): ?string
    {
        return 'authenticated_user';
    }
    public function processAuthenticate($signature, $extra = null): array
    {
        $challenge = $this->getChallengeFromExtra($extra);

        if (!$challenge || !isset($challenge->authid)) {
            return ['FAILURE'];
        }

        $authid = $challenge->authid;
        $key = $this->getKeyFromAuthId($authid);

        if (null === $key) {
            return ['FAILURE'];
        }

        $authDetails = [
            'authmethod' => $this->getMethodName(),
            'authrole' => $this->getRoleFromAuthId($authid),
            'authid' => $challenge->authid,
        ];

        return ['SUCCESS', $authDetails];
    }

}
