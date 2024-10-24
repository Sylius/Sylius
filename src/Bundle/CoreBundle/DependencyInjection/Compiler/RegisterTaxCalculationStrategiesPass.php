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

namespace Sylius\Bundle\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterTaxCalculationStrategiesPass implements CompilerPassInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.tax_calculation_strategy')) {
            return;
        }

        $registry = $container->findDefinition('sylius.registry.tax_calculation_strategy');
        $strategies = [];

        foreach ($container->findTaggedServiceIds('sylius.taxation.calculation_strategy') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'], $attribute['label'])) {
                    throw new \InvalidArgumentException('Tagged tax calculation strategies need to have `type` and `label` attributes.');
                }

                $priority = (int) ($attribute['priority'] ?? 0);

                $strategies[$attribute['type']] = $attribute['label'];

                $registry->addMethodCall('register', [new Reference($id), $priority]);
            }
        }

        $container->setParameter('sylius.tax_calculation_strategies', $strategies);
    }
}
