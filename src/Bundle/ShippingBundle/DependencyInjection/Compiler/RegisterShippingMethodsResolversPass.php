<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShippingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterShippingMethodsResolversPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.shipping_methods_resolver')) {
            return;
        }

        $registry = $container->findDefinition('sylius.registry.shipping_methods_resolver');
        $resolvers = [];

        foreach ($container->findTaggedServiceIds('sylius.shipping_method_resolver') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['label'])) {
                    throw new \InvalidArgumentException('Tagged shipping methods resolvers need to have `type` and `label` attributes.');
                }

                $priority = (int) ($attribute['priority'] ?? 0);

                $resolvers[$attribute['type']] = $attribute['label'];

                $registry->addMethodCall('register', [new Reference($id), $priority]);
            }
        }

        $container->setParameter('sylius.shipping_method_resolvers', $resolvers);
    }
}
