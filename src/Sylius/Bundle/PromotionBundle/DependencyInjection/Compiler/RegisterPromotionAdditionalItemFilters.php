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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterPromotionAdditionalItemFilters implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('sylius.registry_promotion_additional_item_filters')) {
            return;
        }

        $promotionAdditionalItemFiltersRegistry = $container->getDefinition('sylius.registry_promotion_additional_item_filters');

        foreach ($container->findTaggedServiceIds('sylius.promotion_additional_item_filters') as $id => $attributes) {
            $promotionAdditionalItemFiltersRegistry->addMethodCall('register', [$id, new Reference($id)]);
        }
    }
}
