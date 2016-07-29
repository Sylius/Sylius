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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\UserBundle\Controller\UserController;
use Sylius\Bundle\UserBundle\Form\Type\UserRegistrationType;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Model\UserOAuth;
use Sylius\Component\User\Model\UserOAuthInterface;
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
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
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
                ->arrayNode('verification')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('token')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('length')
                                    ->defaultValue(16)
                                    ->min(1)->max(40)
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $node
     */
    private function addResourcesSection(ArrayNodeDefinition $node)
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('user')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('templates')->defaultValue('SyliusUserBundle:User')->end()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(User::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(UserInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(UserController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->arrayNode('form')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('default')->defaultValue(UserType::class)->cannotBeEmpty()->end()
                                                ->scalarNode('registration')->defaultValue(UserRegistrationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                                ->arrayNode('validation_groups')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->arrayNode('default')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['sylius'])
                                        ->end()
                                        ->arrayNode('registration')
                                            ->prototype('scalar')->end()
                                            ->defaultValue(['sylius', 'sylius_user_registration'])
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('user_oauth')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(UserOAuth::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(UserOAuthInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
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
