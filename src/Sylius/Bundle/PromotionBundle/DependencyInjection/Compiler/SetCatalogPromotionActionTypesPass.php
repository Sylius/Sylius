<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Sylius\Bundle\CoreBundle\Calculator\ActionBasedPriceCalculatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetCatalogPromotionActionTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $types = [];
        foreach ($container->findTaggedServiceIds('sylius.catalog_promotion.price_calculator') as $id => $attributes) {
            $definition = $container->getDefinition($id);
            /** @var ActionBasedPriceCalculatorInterface $class */
            $class = $definition->getClass();
            $types[] = $class::getType();
        }

        $container->setParameter('sylius.catalog_promotion.actions', $types);
    }
}
