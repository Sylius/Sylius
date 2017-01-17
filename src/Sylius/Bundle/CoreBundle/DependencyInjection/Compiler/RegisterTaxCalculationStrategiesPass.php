<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class RegisterTaxCalculationStrategiesPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.tax_calculation_strategy')) {
            return;
        }

        $registry = $container->findDefinition('sylius.registry.tax_calculation_strategy');
        $strategies = [];

        foreach ($container->findTaggedServiceIds('sylius.taxation.calculation_strategy') as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged tax calculation strategies need to have `type` and `label` attributes.');
            }

            $priority = isset($attributes[0]['priority']) ? (int) $attributes[0]['priority'] : 0;

            $strategies[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('register', [new Reference($id), $priority]);
        }

        $container->setParameter('sylius.tax_calculation_strategies', $strategies);
    }
}
