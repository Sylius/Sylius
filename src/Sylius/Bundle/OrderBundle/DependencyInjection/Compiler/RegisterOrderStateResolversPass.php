<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class RegisterOrderStateResolversPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.order_processing.state_resolver')) {
            return;
        }

        $stateResolver = $container->findDefinition('sylius.order_processing.state_resolver');

        foreach ($container->findTaggedServiceIds('sylius.order.state_resolver') as $id => $attributes) {
            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $stateResolver->addMethodCall('addResolver', [new Reference($id), $priority]);
        }
    }
}
