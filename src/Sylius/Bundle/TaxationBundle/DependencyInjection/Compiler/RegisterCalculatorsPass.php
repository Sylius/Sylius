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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RegisterCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.tax_calculator')) {
            return;
        }

        $calculatorRegistry = $container->getDefinition('sylius.registry.tax_calculator');
        $calculators = [];

        foreach ($container->findTaggedServiceIds('sylius.tax_calculator') as $id => $attributes) {
            if (!isset($attributes[0]['calculator'])) {
                throw new \InvalidArgumentException('Tagged taxation calculators needs to have `calculator` attribute.');
            }

            $name = $attributes[0]['calculator'];
            $calculators[$name] = $name;

            $calculatorRegistry->addMethodCall('register', [$name, new Reference($id)]);
        }

        $container->setParameter('sylius.tax_calculators', $calculators);
    }
}
