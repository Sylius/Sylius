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
            ->end()
        ;

        $this->addClassesSection($rootNode);
        $this->addEmailsSection($rootNode);
        $this->addCheckoutSection($rootNode);

        return $treeBuilder;
    }

    protected function addEmailsSection(ArrayNodeDefinition $node)
    {
        $emailNode = $node->children()->arrayNode('emails');

        $emailNode
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
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
                ->children()
                    ->booleanNode('enabled')->defaultTrue()->end()
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
                        ->arrayNode('block')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\CoreBundle\Form\Type\BlockType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('page')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Symfony\Cmf\Bundle\ContentBundle\Doctrine\Phpcr\StaticContent')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\CoreBundle\Form\Type\PageType')->end()
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
