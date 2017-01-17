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

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\UserBundle\Controller\UserController;
use Sylius\Bundle\UserBundle\Form\Type\UserType;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class Configuration implements ConfigurationInterface
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
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('user')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('templates')->defaultValue('SyliusUserBundle:User')->end()
                                    ->variableNode('options')->end()
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
                                                    ->scalarNode('field_name')
                                                        ->defaultValue('passwordResetToken')
                                                        ->validate()
                                                        ->ifTrue(function ($tokenFieldName) {
                                                            return !is_string($tokenFieldName);
                                                        })
                                                            ->thenInvalid('Invalid resetting token field "%s"')
                                                        ->end()
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
                                                    ->scalarNode('field_name')
                                                        ->defaultValue('passwordResetToken')
                                                        ->validate()
                                                        ->ifTrue(function ($passwordResetToken) {
                                                            return !is_string($passwordResetToken);
                                                        })
                                                            ->thenInvalid('Invalid resetting pin field "%s"')
                                                        ->end()
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
                                                    ->scalarNode('field_name')
                                                        ->defaultValue('emailVerificationToken')
                                                        ->validate()
                                                        ->ifTrue(function ($emailVerificationToken) {
                                                            return !is_string($emailVerificationToken);
                                                        })
                                                            ->thenInvalid('Invalid verification token field "%s"')
                                                        ->end()
                                                    ->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('classes')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->defaultValue(User::class)->cannotBeEmpty()->end()
                                            ->scalarNode('interface')->defaultValue(UserInterface::class)->cannotBeEmpty()->end()
                                            ->scalarNode('controller')->defaultValue(UserController::class)->cannotBeEmpty()->end()
                                            ->scalarNode('repository')->cannotBeEmpty()->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('form')->cannotBeEmpty()->end()
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
