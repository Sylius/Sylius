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

class ShippingMethodFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'shipping_method';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->scalarNode('zone')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('category')->end()
                ->variableNode('channels')
                    ->beforeNormalization()
                        ->ifNull()->thenUnset()
                    ->end()
                ->end()
                ->arrayNode('calculator')
                    ->children()
                        ->scalarNode('type')->isRequired()->cannotBeEmpty()->end()
                        ->variableNode('configuration')->end()
                    ->end()
                ->end()
                ->scalarNode('tax_category')->end()
        ;
    }
}
