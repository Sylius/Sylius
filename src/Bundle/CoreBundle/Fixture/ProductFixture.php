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

class ProductFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'product';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->booleanNode('tracked')->end()
                ->scalarNode('slug')->end()
                ->scalarNode('short_description')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->scalarNode('main_taxon')->cannotBeEmpty()->end()
                ->arrayNode('taxons')->scalarPrototype()->end()->end()
                ->variableNode('channels')
                    ->beforeNormalization()
                        ->ifNull()->thenUnset()
                    ->end()
                ->end()
                ->scalarNode('variant_selection_method')->end()
                ->arrayNode('product_attributes')->variablePrototype()->end()->end()
                ->arrayNode('product_options')->scalarPrototype()->end()->end()
                ->arrayNode('images')->variablePrototype()->end()->end()
                ->booleanNode('shipping_required')->end()
                ->scalarNode('tax_category')->end()
        ;
    }
}
