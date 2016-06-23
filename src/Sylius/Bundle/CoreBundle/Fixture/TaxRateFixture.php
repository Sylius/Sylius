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
final class TaxRateFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tax_rate';
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
                ->floatNode('amount')->cannotBeEmpty()->end()
                ->booleanNode('included_in_price')->end()
                ->scalarNode('calculator')->cannotBeEmpty()->end()
                ->scalarNode('zone')->cannotBeEmpty()->end()
                ->scalarNode('tax_category')->cannotBeEmpty()->end()
        ;
    }
}
