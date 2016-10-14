<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class RegisterShippingMethodsResolversPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.shipping_methods_resolver')) {
            return;
        }

        $registry = $container->findDefinition('sylius.registry.shipping_methods_resolver');
        $resolvers = [];

        foreach ($container->findTaggedServiceIds('sylius.shipping_method_resolver') as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged shipping methods resolvers need to have `type` and `label` attributes.');
            }

            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $resolvers[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('register', [new Reference($id), $priority]);
        }

        $container->setParameter('sylius.shipping_method_resolvers', $resolvers);
    }
}
