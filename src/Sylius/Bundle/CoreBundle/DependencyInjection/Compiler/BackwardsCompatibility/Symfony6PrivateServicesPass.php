<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\BackwardsCompatibility;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/** @internal */
final class Symfony6PrivateServicesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // Some services are still fetched directly from the container in both Sylius and its extracted bundles (e.g. ResourceBundle)
        // Before we fix it let's make them public to keep backward compatibility

        $this->makeServicePublic('form.factory', $container);
        $this->makeServicePublic('security.authorization_checker', $container);
        $this->makeServicePublic('security.authorization_checker', $container);
        $this->makeServicePublic('security.csrf.token_manager', $container);
        $this->makeServicePublic('security.csrf.token_manager', $container);
        $this->makeServicePublic('security.token_storage', $container);
        $this->makeServicePublic('security.token_storage', $container);
        $this->makeServicePublic('validator', $container);

        if (str_starts_with($container->getParameter('kernel.environment'), 'test')) {
            $this->makeServicePublic('filesystem', $container);
            $this->makeServicePublic('session.factory', $container);
            $this->makeServicePublic('sylius.command_bus', $container);
        }

        $tokenStorage = $container->getDefinition('security.token_storage');
        $tokenStorage->clearTag('kernel.reset');
        $container->setDefinition('security.token_storage', $tokenStorage);
    }

    private function makeServicePublic(string $serviceId, ContainerBuilder $container): void
    {
        $service = $container->getDefinition($serviceId);
        $service->setPublic(true);
        $container->setDefinition($serviceId, $service);
    }
}
