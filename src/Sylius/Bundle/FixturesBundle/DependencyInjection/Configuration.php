<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_fixtures');

        /** @var ArrayNodeDefinition $suiteNode */
        $suiteNode = $rootNode
            ->children()
                ->arrayNode('suites')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
        ;

        /** @var ArrayNodeDefinition $fixtureNode */
        $fixtureNode = $suiteNode
            ->children()
                ->arrayNode('fixtures')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
        ;

        $fixtureAttributesNodeBuilder = $fixtureNode->canBeUnset()->children();
        $fixtureAttributesNodeBuilder->integerNode('priority')->defaultValue(0);

        /** @var ArrayNodeDefinition $fixtureOptionsNode */
        $fixtureOptionsNode = $fixtureAttributesNodeBuilder->arrayNode('options');
        $fixtureOptionsNode
            ->addDefaultChildrenIfNoneSet()
            ->beforeNormalization()
                ->always(function ($value) {
                    return [$value];
                })
            ->end()
        ;
        $fixtureOptionsNode->prototype('variable');

        return $treeBuilder;
    }
}
