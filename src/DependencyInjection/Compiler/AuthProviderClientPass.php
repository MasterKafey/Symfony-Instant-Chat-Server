<?php

namespace App\DependencyInjection\Compiler;

use App\WampServer\WampRouter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AuthProviderClientPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(WampRouter::class)) {
            return;
        }

        $definition = $container->getDefinition(WampRouter::class);
        $taggedServices = $container->findTaggedServiceIds('app.server.security.auth_provider_client');

        foreach ($taggedServices as $id => $taggedService) {
            $definition->addMethodCall('addAuthProviderClient', [new Reference($id)]);
        }
    }
}
