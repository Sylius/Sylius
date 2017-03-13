<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AdminApiBundle\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ApiAccessTokenFixture extends AbstractResourceFixture
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'access_token';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode)
    {
        $resourceNode
            ->children()
                ->scalarNode('client')->cannotBeEmpty()->end()
                ->scalarNode('token')->cannotBeEmpty()->end()
                ->scalarNode('user')->cannotBeEmpty()->end()
                ->scalarNode('expires_at')->end()
        ;
    }
}
