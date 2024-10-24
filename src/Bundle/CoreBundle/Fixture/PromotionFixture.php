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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PromotionFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'promotion';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->integerNode('usage_limit')->end()
                ->booleanNode('coupon_based')->end()
                ->booleanNode('exclusive')->end()
                ->integerNode('priority')->min(0)->end()
                ->variableNode('channels')
                    ->beforeNormalization()
                        ->ifNull()->thenUnset()
                    ->end()
                ->end()
                ->scalarNode('starts_at')->cannotBeEmpty()->end()
                ->scalarNode('ends_at')->cannotBeEmpty()->end()
                ->scalarNode('archived_at')->defaultNull()->end()
                ->arrayNode('rules')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')->cannotBeEmpty()->end()
                            ->variableNode('configuration')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('actions')
                    ->requiresAtLeastOneElement()
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('type')->cannotBeEmpty()->end()
                            ->variableNode('configuration')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('coupons')->arrayPrototype()
                    ->children()
                        ->scalarNode('code')->cannotBeEmpty()->end()
                        ->scalarNode('expires_at')->defaultNull()->end()
                        ->integerNode('per_customer_usage_limit')->defaultNull()->end()
                        ->booleanNode('reusable_from_cancelled_orders')->defaultTrue()->end()
                        ->integerNode('usage_limit')->defaultNull()->end()
                    ->end()
                ->end()
        ;
    }
}
