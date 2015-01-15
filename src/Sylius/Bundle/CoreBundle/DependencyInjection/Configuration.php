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
                ->scalarNode('driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('currency_storage')->defaultValue('sylius.storage.session')->end()
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addEmailsSection($rootNode);
        $this->addRoutingSection($rootNode);
        $this->addCheckoutSection($rootNode);

        return $treeBuilder;
    }

    protected function addEmailsSection(ArrayNodeDefinition $node)
    {
        $emailNode = $node->children()->arrayNode('emails');

        $emailNode
            ->addDefaultsIfNotSet()
            ->canBeEnabled()
            ->children()
                ->arrayNode('from_email')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('address')->defaultValue('webmaster@example.com')->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->defaultValue('webmaster')->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        $this->addEmailConfiguration($emailNode, 'order_confirmation', 'SyliusWebBundle:Frontend/Email:orderConfirmation.html.twig');
        $this->addEmailConfiguration($emailNode, 'order_comment', 'SyliusWebBundle:Frontend/Email:orderComment.html.twig');
        $this->addEmailConfiguration($emailNode, 'customer_welcome', 'SyliusWebBundle:Frontend/Email:customerWelcome.html.twig');

        return $emailNode;
    }

    /**
     * Helper method to configure a single email type
     *
     * @param ArrayNodeDefinition $node
     * @param string              $name
     * @param string              $template
     */
    protected function addEmailConfiguration(ArrayNodeDefinition $node, $name, $template)
    {
        $node
            ->children()
                ->arrayNode($name)
                ->addDefaultsIfNotSet()
                ->canBeUnset()
                ->canBeEnabled()
                ->children()
                    ->scalarNode('template')->defaultValue($template)->end()
                    ->arrayNode('from_email')
                    ->canBeUnset()
                    ->children()
                        ->scalarNode('address')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('sender_name')->isRequired()->cannotBeEmpty()->end()
                    ->end()
                ->end()
            ->end()
        ->end();
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
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Core\Model\User')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\CoreBundle\Form\Type\UserType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_oauth')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Core\Model\UserOAuth')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                            ->end()
                        ->end()
                        ->arrayNode('group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Core\Model\Group')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\CoreBundle\Form\Type\GroupType')->end()
                            ->end()
                        ->end()
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
