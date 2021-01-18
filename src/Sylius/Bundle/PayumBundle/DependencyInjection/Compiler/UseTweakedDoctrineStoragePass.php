<?php

declare(strict_types=1);

namespace Sylius\Bundle\PayumBundle\DependencyInjection\Compiler;

use Sylius\Bundle\PayumBundle\Storage\DoctrineStorage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @internal
 */
final class UseTweakedDoctrineStoragePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $container->setParameter('payum.storage.doctrine.orm.class', DoctrineStorage::class);
    }
}
