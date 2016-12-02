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
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductAssociationFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'product_association';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('type')->cannotBeEmpty()->end()
                ->scalarNode('owner')->cannotBeEmpty()->end()
                ->arrayNode('associated_products')->prototype('scalar')->end()->end()
        ;
    }
}
