<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ListenerRegistryPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sylius_fixtures.listener_registry')) {
            return;
        }

        $listenerRegistry = $container->findDefinition('sylius_fixtures.listener_registry');

        $taggedServices = $container->findTaggedServiceIds('sylius_fixtures.listener');
        foreach (array_keys($taggedServices) as $id) {
            $listenerRegistry->addMethodCall('addListener', [new Reference($id)]);
        }
    }
}
