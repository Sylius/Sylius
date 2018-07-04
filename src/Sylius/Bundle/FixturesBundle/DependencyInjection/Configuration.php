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

namespace Sylius\Bundle\FixturesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_fixtures');

        $this->buildSuitesNode($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function buildSuitesNode(ArrayNodeDefinition $rootNode): void
    {
        /** @var ArrayNodeDefinition $suitesNode */
        $suitesNode = $rootNode
            ->children()
                ->arrayNode('suites')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
        ;

        $suitesNode
            ->validate()
                ->ifArray()
                ->then(function (array $value) {
                    if (!isset($value['fixtures'])) {
                        return $value;
                    }

                    foreach ($value['fixtures'] as $fixtureKey => &$fixtureValue) {
                        if (!isset($fixtureValue['name'])) {
                            $fixtureValue['name'] = $fixtureKey;
                        }
                    }

                    return $value;
                })
        ;

        $this->buildFixturesNode($suitesNode);
        $this->buildListenersNode($suitesNode);
    }

    /**
     * @param ArrayNodeDefinition $suitesNode
     */
    private function buildFixturesNode(ArrayNodeDefinition $suitesNode): void
    {
        /** @var ArrayNodeDefinition $fixturesNode */
        $fixturesNode = $suitesNode
            ->children()
                ->arrayNode('fixtures')
                    ->useAttributeAsKey('alias')
                    ->arrayPrototype()
        ;

        $fixturesNode->children()->scalarNode('name')->cannotBeEmpty();

        $this->buildAttributesNode($fixturesNode);
    }

    /**
     * @param ArrayNodeDefinition $suitesNode
     */
    private function buildListenersNode(ArrayNodeDefinition $suitesNode): void
    {
        /** @var ArrayNodeDefinition $listenersNode */
        $listenersNode = $suitesNode
            ->children()
                ->arrayNode('listeners')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
        ;

        $this->buildAttributesNode($listenersNode);
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function buildAttributesNode(ArrayNodeDefinition $node): void
    {
        $attributesNodeBuilder = $node->canBeUnset()->children();
        $attributesNodeBuilder->integerNode('priority')->defaultValue(0);

        /** @var ArrayNodeDefinition $optionsNode */
        $optionsNode = $attributesNodeBuilder->arrayNode('options');
        $optionsNode->addDefaultChildrenIfNoneSet();

        $optionsNode
            ->validate()
                ->ifTrue(function (array $values) {
                    foreach ($values as $value) {
                        if (!is_array($value)) {
                            return true;
                        }
                    }

                    return false;
                })
                ->thenInvalid('Options have to be an array!')
        ;

        $optionsNode
            ->beforeNormalization()
                ->always(function ($value) {
                    return [$value];
                })
        ;

        $optionsNode->variablePrototype()->cannotBeEmpty()->defaultValue([]);
    }
}
