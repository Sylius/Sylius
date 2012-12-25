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
 * Registers all shipping calculators in delegating service.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RegisterCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius_shipping.calculator')) {
            return;
        }

        $delegatingCalculator = $container->getDefinition('sylius_shipping.calculator');
        $calculators = array();

        foreach ($container->findTaggedServiceIds('sylius_shipping.calculator') as $id => $attributes) {
            $name = $attributes[0]['calculator'];

            $calculators[$name] = $name;

            $delegatingCalculator->addMethodCall('registerCalculator', array($name, new Reference($id)));
        }

        $container->setParameter('sylius_shipping.calculators', $calculators);
    }
}
