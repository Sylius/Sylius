<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all calculators in container.
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
        if (!$container->hasDefinition('sylius.tax_calculator')) {
            return;
        }

        $delegatingCalculator = $container->getDefinition('sylius.tax_calculator');
        $calculators = array();

        foreach ($container->findTaggedServiceIds('sylius.tax_calculator') as $id => $attributes) {
            $name = $attributes[0]['calculator'];

            $calculators[$name] = $name;

            $delegatingCalculator->addMethodCall('registerCalculator', array($name, new Reference($id)));
        }

        $container->setParameter('sylius.tax_calculators', $calculators);
    }
}
