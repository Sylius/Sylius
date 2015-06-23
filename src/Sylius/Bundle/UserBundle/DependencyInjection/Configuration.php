<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle.
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 *
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sylius_user');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end()
                ->arrayNode('resetting')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('token')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('ttl')->defaultValue('P1D')->end()
                                ->integerNode('length')
                                    ->defaultValue(16)
                                    ->min(1)->max(40)
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('pin')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('length')
                                    ->defaultValue(4)
                                    ->min(1)->max(9)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
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
                        ->arrayNode('customer')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius', 'sylius_customer_profile'))
                        ->end()
                        ->arrayNode('customer_profile')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius', 'sylius_customer_profile'))
                        ->end()
                        ->arrayNode('customer_registration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius', 'sylius_customer_profile', 'sylius_user_registration'))
                        ->end()
                        ->arrayNode('user')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius'))
                        ->end()
                        ->arrayNode('user_registration')
                            ->prototype('scalar')->end()
                            ->defaultValue(array('sylius', 'sylius_user_registration'))
                        ->end()
                        ->arrayNode('group')
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
                        ->arrayNode('customer')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\User\Model\Customer')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\UserBundle\Controller\CustomerController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\UserBundle\Form\Type\CustomerType')->end()
                                        ->scalarNode('profile')->defaultValue('Sylius\Bundle\UserBundle\Form\Type\CustomerProfileType')->end()
                                        ->scalarNode('registration')->defaultValue('Sylius\Bundle\UserBundle\Form\Type\CustomerRegistrationType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\User\Model\User')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\UserBundle\Controller\UserController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\UserBundle\Form\Type\UserType')->end()
                                        ->scalarNode('registration')->defaultValue('Sylius\Bundle\UserBundle\Form\Type\UserRegistrationType')->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_oauth')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\User\Model\UserOAuth')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                            ->end()
                        ->end()
                        ->arrayNode('group')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('model')->defaultValue('Sylius\Component\User\Model\Group')->end()
                                ->scalarNode('controller')->defaultValue('Sylius\Bundle\ResourceBundle\Controller\ResourceController')->end()
                                ->scalarNode('repository')->end()
                                ->arrayNode('form')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('default')->defaultValue('Sylius\Bundle\UserBundle\Form\Type\GroupType')->end()
                                        ->scalarNode('choice')->defaultValue('%sylius.form.type.resource_choice.class%')->end()
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
