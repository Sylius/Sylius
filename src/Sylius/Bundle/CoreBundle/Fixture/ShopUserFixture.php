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
class ShopUserFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'shop_user';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('email')->cannotBeEmpty()->end()
                ->scalarNode('first_name')->cannotBeEmpty()->end()
                ->scalarNode('last_name')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->scalarNode('password')->cannotBeEmpty()->end()
                ->scalarNode('customer_group')->end()
        ;
    }
}
