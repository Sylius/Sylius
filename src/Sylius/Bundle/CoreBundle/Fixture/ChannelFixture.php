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
class ChannelFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'channel';
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
                ->scalarNode('hostname')->cannotBeEmpty()->end()
                ->scalarNode('color')->cannotBeEmpty()->end()
                ->scalarNode('default_tax_zone')->end()
                ->scalarNode('tax_calculation_strategy')->end()
                ->booleanNode('enabled')->end()
                ->booleanNode('skipping_shipping_step_allowed')->end()
                ->booleanNode('skipping_payment_step_allowed')->end()
                ->scalarNode('default_locale')->cannotBeEmpty()->end()
                ->arrayNode('locales')->prototype('scalar')->end()->end()
                ->scalarNode('base_currency')->cannotBeEmpty()->end()
                ->arrayNode('currencies')->prototype('scalar')->end()->end()
                ->scalarNode('theme_name')->end()
                ->scalarNode('contact_email')->end()
        ;
    }
}
