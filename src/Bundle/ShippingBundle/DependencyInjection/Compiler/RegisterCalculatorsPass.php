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

final class RegisterCalculatorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.shipping_calculator') || !$container->hasDefinition('sylius.form_registry.shipping_calculator')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.shipping_calculator');
        $formTypeRegistry = $container->getDefinition('sylius.form_registry.shipping_calculator');
        $calculators = [];

        foreach ($container->findTaggedServiceIds('sylius.shipping_calculator') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['calculator'], $attribute['label'])) {
                    throw new \InvalidArgumentException('Tagged shipping calculators needs to have `calculator` and `label` attributes.');
                }

                $name = $attribute['calculator'];
                $calculators[$name] = $attribute['label'];

                $registry->addMethodCall('register', [$name, new Reference($id)]);

                if (isset($attribute['form_type'])) {
                    $formTypeRegistry->addMethodCall('add', [$name, 'default', $attribute['form_type']]);
                }
            }
        }

        $container->setParameter('sylius.shipping_calculators', $calculators);
    }
}
