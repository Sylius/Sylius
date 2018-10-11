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

class ChannelFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'channel';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('name')->cannotBeEmpty()->end()
                ->scalarNode('code')->cannotBeEmpty()->end()
                ->scalarNode('hostname')->end()
                ->scalarNode('color')->cannotBeEmpty()->end()
                ->scalarNode('default_tax_zone')->end()
                ->scalarNode('tax_calculation_strategy')->end()
                ->booleanNode('enabled')->end()
                ->booleanNode('skipping_shipping_step_allowed')->end()
                ->booleanNode('skipping_payment_step_allowed')->end()
                ->booleanNode('account_verification_required')->end()
                ->scalarNode('default_locale')->cannotBeEmpty()->end()
                ->arrayNode('locales')->scalarPrototype()->end()->end()
                ->scalarNode('base_currency')->cannotBeEmpty()->end()
                ->arrayNode('currencies')->scalarPrototype()->end()->end()
                ->scalarNode('theme_name')->end()
                ->scalarNode('contact_email')->end()
        ;
    }
}
