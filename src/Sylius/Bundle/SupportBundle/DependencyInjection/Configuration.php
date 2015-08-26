<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SupportBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_support');

        $rootNode
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
            ->end()
        ;

        $this->addValidationGroupsSection($rootNode);
        $this->addClassesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addValidationGroupsSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('validation_groups')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('support_ticket')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('support_category')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('support_category_translation')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addClassesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('support_ticket')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Support\Model\SupportTicket')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\SupportBundle\Controller\SupportTicketController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\SupportBundle\Form\Type\SupportTicketType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('support_category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\Support\Model\SupportCategory')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->defaultValue('Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\SupportBundle\Form\Type\SupportCategoryType')->end()
                                        ->scalarNode('choice')->defaultValue('Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType')->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue('Sylius\Component\Support\Model\SupportCategoryTranslation')->end()
                                        ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                        ->scalarNode('repository')->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue('Sylius\Bundle\SupportBundle\Form\Type\SupportCategoryTranslationType')->end()
                                            ->end()
                                        ->end()
                                        ->arrayNode('mapping')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->arrayNode('fields')
                                                ->prototype('scalar')->end()
                                                    ->defaultValue(array('title'))
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
