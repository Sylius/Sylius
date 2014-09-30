<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\NewsletterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_newsletter');

        $rootNode
            ->children()
                ->scalarNode('driver')->isRequired()->cannotBeEmpty()->end()
            ->end()
        ;

        $this->addValidationGroupsSection($rootNode);
        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * Adds `validation_groups` section.
     *
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('newsletter_subscriber')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('newsletter_subscription_list')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
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
                        ->arrayNode('newsletter_subscriber')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Newsletter\Model\Subscriber')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\NewsletterBundle\Form\Type\SubscriberType')->end()
                            ->end()
                        ->end()
                        ->arrayNode('newsletter_subscription_list')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Newsletter\Model\SubscriptionList')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->scalarNode('form')->defaultValue('Sylius\Bundle\NewsletterBundle\Form\Type\SubscriptionListType')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
