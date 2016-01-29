<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PricingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers all price calculators in the container.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RegisterPriceCalculatorsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('sylius.registry.price_calculator')) {
            return;
        }

        $registry = $container->getDefinition('sylius.registry.price_calculator');
        $calculators = [];

        foreach ($container->findTaggedServiceIds('sylius.price_calculator') as $id => $attributes) {
            if (!isset($attributes[0]['type']) || !isset($attributes[0]['label'])) {
                throw new \InvalidArgumentException('Tagged price calculator needs to have `type` and `label` attributes.');
            }

            $calculators[$attributes[0]['type']] = $attributes[0]['label'];

            $registry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        }

        $container->setParameter('sylius.price_calculators', $calculators);
    }
}
