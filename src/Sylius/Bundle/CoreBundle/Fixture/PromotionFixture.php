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

namespace Sylius\Bundle\CoreBundle\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class PromotionFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'promotion';
    }

    /**
     * {@inheritdoc}
     */
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
                ->arrayNode('channels')->scalarPrototype()->end()->end()
                ->scalarNode('starts_at')->cannotBeEmpty()->end()
                ->scalarNode('ends_at')->cannotBeEmpty()->end()
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
        ;
    }
}
