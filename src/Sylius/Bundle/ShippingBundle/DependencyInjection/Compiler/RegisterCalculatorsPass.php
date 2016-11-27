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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RegisterCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.shipping_calculator') || !$container->hasDefinition('sylius.form_registry.shipping_calculator')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.shipping_calculator');
        $formTypeRegistry = $container->getDefinition('sylius.form_registry.shipping_calculator');
        $calculators = [];

        foreach ($container->findTaggedServiceIds('sylius.shipping_calculator') as $id => $attributes) {
            if (!isset($attributes[0]['calculator'], $attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged shipping calculators needs to have `calculator` and `label` attributes.');
            }

            $name = $attributes[0]['calculator'];
            $calculators[$name] = $attributes[0]['label'];

            $registry->addMethodCall('register', [$name, new Reference($id)]);

            if (isset($attributes[0]['form-type'])) {
                $formTypeRegistry->addMethodCall('add', [$name, 'default', $attributes[0]['form-type']]);
            }
        }

        $container->setParameter('sylius.shipping_calculators', $calculators);
    }
}
