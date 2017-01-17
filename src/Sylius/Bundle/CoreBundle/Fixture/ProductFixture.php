<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ProductFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('short_description')->cannotBeEmpty()->end()
                ->scalarNode('description')->cannotBeEmpty()->end()
                ->scalarNode('main_taxon')->cannotBeEmpty()->end()
                ->arrayNode('taxons')->prototype('scalar')->end()->end()
                ->arrayNode('channels')->prototype('scalar')->end()->end()
                ->arrayNode('product_attributes')->prototype('scalar')->end()->end()
                ->arrayNode('product_reviews')->prototype('scalar')->end()->end()
                ->arrayNode('product_options')->prototype('scalar')->end()->end()
                ->arrayNode('images')->prototype('scalar')->end()->end()
                ->booleanNode('shipping_required')->end()
        ;
    }
}
