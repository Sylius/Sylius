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

namespace Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterCalculatorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('sylius.registry.tax_calculator')) {
            return;
        }

        $calculatorRegistry = $container->getDefinition('sylius.registry.tax_calculator');
        $calculators = [];

        foreach ($container->findTaggedServiceIds('sylius.tax_calculator') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['calculator'])) {
                    throw new \InvalidArgumentException('Tagged taxation calculators needs to have `calculator` attribute.');
                }

                $name = $attribute['calculator'];
                $calculators[$name] = $name;

                $calculatorRegistry->addMethodCall('register', [$name, new Reference($id)]);
            }
        }

        $container->setParameter('sylius.tax_calculators', $calculators);
    }
}
