<?php

namespace App\WampServer;

use React\EventLoop\Loop;
use Thruway\Authentication\AbstractAuthProviderClient;
use Thruway\Authentication\AuthenticationManager;
use Thruway\Authentication\AuthorizationManager;
use Thruway\Peer\Router;

class WampRouter extends Router
{
    public const REALM_USER = 'user';

    public function __construct(
        private readonly string $wampURL,
    )
    {
        parent::__construct(Loop::get());
        $this->addTransportProvider(new RouterTransportProvider($this->wampURL));
        $authenticationManager = new AuthenticationManager();
        $authorizationManager = new AuthorizationManager(WampRouter::REALM_USER, Loop::get());
        $authorizationManager->addAuthorizationRule([
            [
                'role' => 'authenticated_user',
                'action' => 'subscribe',
                'uri' => '*',
                'allow' => true,
            ],
            [
                'role' => 'authenticated_user',
                'action' => 'publish',
                'uri' => '*',
                'allow' => true,
            ],
            [
                'role' => 'internal',
                'action' => 'publish',
                'uri' => '*',
                'allow' => true,
            ],
        ]);
        $this->registerModule($authenticationManager);
    }

    public function addAuthProviderClient(AbstractAuthProviderClient $authProviderClient): void
    {
        $this->addInternalClient($authProviderClient);
    }
}
