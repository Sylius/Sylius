<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\UserBundle\Controller\UserController;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_user');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('encoder')->defaultNull()->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        $node
            ->children()
                ->arrayNode('resources')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->arrayNode('user')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('templates')->defaultValue('SyliusUserBundle:User')->end()
                                    ->scalarNode('encoder')->defaultNull()->end()
                                    ->scalarNode('login_tracking_interval')->defaultNull()->end()
                                    ->variableNode('options')
                                        ->setDeprecated('sylius/user-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                    ->end()
                                    ->arrayNode('resetting')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->arrayNode('token')
                                                ->addDefaultsIfNotSet()
                                                ->children()
                                                    ->scalarNode('ttl')
                                                        ->defaultValue('P1D')
                                                        ->validate()
                                                        ->ifTrue(
                                                            function (mixed $ttl) {
                                                                try {
                                                                    new \DateInterval($ttl);

                                                                    return false;
                                                                } catch (\Exception) {
                                                                    return true;
                                                                }
                                                            },
                                                        )
                                                            ->thenInvalid('Invalid format for TTL "%s". Expected a string compatible with DateInterval.')
                                                        ->end()
                                                    ->end()
                                                    ->integerNode('length')
                                                        ->defaultValue(64)
                                                        ->min(1)->max(255)
                                                    ->end()
                                                    ->scalarNode('field_name')
                                                        ->defaultValue('passwordResetToken')
                                                        ->validate()
                                                        ->ifTrue(
                                                            /** @param mixed $tokenFieldName */
                                                            function ($tokenFieldName) {
                                                                return !is_string($tokenFieldName);
                                                            },
                                                        )
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
                                                        ->ifTrue(
                                                            /** @param mixed $passwordResetToken */
                                                            function ($passwordResetToken) {
                                                                return !is_string($passwordResetToken);
                                                            },
                                                        )
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
                                                        ->defaultValue(64)
                                                        ->min(1)->max(255)
                                                    ->end()
                                                    ->scalarNode('field_name')
                                                        ->defaultValue('emailVerificationToken')
                                                        ->validate()
                                                        ->ifTrue(
                                                            /** @param mixed $emailVerificationToken */
                                                            function ($emailVerificationToken) {
                                                                return !is_string($emailVerificationToken);
                                                            },
                                                        )
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
