<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_core');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('currency_storage')->defaultValue('sylius.storage.session')->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addRoutingSection($rootNode);
        $this->addCheckoutSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `classes` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product_variant_image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Core\Model\ProductVariantImage')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `routing` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addRoutingSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->scalarNode('route_collection_limit')->defaultValue(0)->info('Limit the number of routes that are fetched when getting a collection, set to false to disable the limit.')->end()
                ->scalarNode('route_uri_filter_regexp')->defaultValue('')->info('Regular expression filter which is used to skip the Sylius dynamic router for any request URI that matches.')->end()
                ->arrayNode('routing')->isRequired()->cannotBeEmpty()
                    ->info('Classes for which routes should be generated.')
                    ->useAttributeAsKey('class_name')
                    ->prototype('array')
                    ->children()
                        ->scalarNode('field')->isRequired()->cannotBeEmpty()->info('Field representing the URI path.')->end()
                        ->scalarNode('prefix')->defaultValue('')->info('Prefix applied to all routes.')->end()
                        ->arrayNode('defaults')->isRequired()->cannotBeEmpty()->info('Defaults to add to the generated route.')
                            ->children()
                                ->scalarNode('controller')->isRequired()->cannotBeEmpty()->info('Controller where the request should be routed.')->end()
                                ->scalarNode('repository')->isRequired()->cannotBeEmpty()->info('Repository where the router will find the class.')->end()
                                ->arrayNode('sylius')->isRequired()->cannotBeEmpty()->info('Sylius defaults to add to generated route.')
                                    ->useAttributeAsKey('sylius')
                                    ->prototype('variable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Adds `checkout` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addCheckoutSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('checkout')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('steps')
                            ->addDefaultsIfNotSet()
                            ->info('Templates used for steps in the checkout flow process')
                            ->children()
                                ->append($this->addCheckoutStepNode('security', 'SyliusWebBundle:Frontend/Checkout/Step:security.html.twig'))
                                ->append($this->addCheckoutStepNode('addressing', 'SyliusWebBundle:Frontend/Checkout/Step:addressing.html.twig'))
                                ->append($this->addCheckoutStepNode('shipping', 'SyliusWebBundle:Frontend/Checkout/Step:shipping.html.twig'))
                                ->append($this->addCheckoutStepNode('payment', 'SyliusWebBundle:Frontend/Checkout/Step:payment.html.twig'))
                                ->append($this->addCheckoutStepNode('finalize', 'SyliusWebBundle:Frontend/Checkout/Step:finalize.html.twig'))
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * Helper method to append checkout step nodes.
     *
     * @param $name
     * @param $defaultTemplate
     * @return NodeDefinition
     */
    private function addCheckoutStepNode($name, $defaultTemplate)
    {
        $builder = new TreeBuilder();
        $node = $builder->root($name);

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('template')->defaultValue($defaultTemplate)->end()
            ->end()
        ;

        return $node;
    }
}
