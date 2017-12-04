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

class AddressFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'address';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('first_name')->cannotBeEmpty()->end()
                ->scalarNode('last_name')->cannotBeEmpty()->end()
                ->scalarNode('phone_number')->end()
                ->scalarNode('company')->end()
                ->scalarNode('street')->cannotBeEmpty()->end()
                ->scalarNode('city')->cannotBeEmpty()->end()
                ->scalarNode('postcode')->cannotBeEmpty()->end()
                ->scalarNode('country_code')->cannotBeEmpty()->end()
                ->scalarNode('province_code')->end()
                ->scalarNode('province_name')->end()
                ->scalarNode('customer')->cannotBeEmpty()->end()
        ;
    }
}
