<?php

/*
 * This file is a part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\Fixture;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
abstract class AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    final public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $optionsNode = $treeBuilder->root($this->getName());

        $this->configureOptionsNode($optionsNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $optionsNode
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode)
    {
        // empty
    }
}
