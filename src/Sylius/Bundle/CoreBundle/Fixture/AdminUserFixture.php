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
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AdminUserFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_user';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('email')->cannotBeEmpty()->end()
                ->scalarNode('username')->cannotBeEmpty()->end()
                ->booleanNode('enabled')->end()
                ->booleanNode('api')->end()
                ->scalarNode('password')->cannotBeEmpty()->end()
                ->scalarNode('locale_code')->cannotBeEmpty()->end()
                ->scalarNode('first_name')->cannotBeEmpty()->end()
                ->scalarNode('last_name')->cannotBeEmpty()->end()
        ;
    }
}
