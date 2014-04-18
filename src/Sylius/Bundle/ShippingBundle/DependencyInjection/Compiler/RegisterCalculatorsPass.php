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
 * Registers all shipping calculators in calculator registry service.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class RegisterCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.shipping_calculator_registry')) {
            return;
        }

        $registry = $container->getDefinition('sylius.shipping_calculator_registry');
        $calculators = array();

        foreach ($container->findTaggedServiceIds('sylius.shipping_calculator') as $id => $attributes) {
            if (!isset($attributes[0]['calculator']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged shipping calculators needs to have `calculator` and `label` attributes.');
            }

            $name = $attributes[0]['calculator'];
            $calculators[$name] = $attributes[0]['label'];

            $registry->addMethodCall('registerCalculator', array($name, new Reference($id)));
        }

        $container->setParameter('sylius.shipping_calculators', $calculators);
    }
}
