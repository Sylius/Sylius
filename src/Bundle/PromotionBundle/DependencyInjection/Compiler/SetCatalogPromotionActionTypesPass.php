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

namespace Sylius\Bundle\PromotionBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetCatalogPromotionActionTypesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $types = [];
        foreach ($container->findTaggedServiceIds('sylius.catalog_promotion.price_calculator') as $id => $attributes) {
            foreach ($attributes as $attribute) {
                if (!isset($attribute['type'])) {
                    throw new \InvalidArgumentException('Tagged catalog promotion price calculator `' . $id . '` needs to have `type` attribute.');
                }

                $types[] = $attribute['type'];
            }
        }

        $container->setParameter('sylius.catalog_promotion.actions_types', $types);
    }
}
