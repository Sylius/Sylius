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

namespace Sylius\Bundle\ApiBundle\DependencyInjection;

use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/** @experimental */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_api');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enabled')
                    ->defaultFalse()
                ->end()
                ->booleanNode('legacy_error_handling')
                    ->defaultFalse()
                ->end()
            ->end()
            ->children()
                ->variableNode('product_image_prefix')
                    ->defaultValue('media/image')
                ->end()
            ->end()
            ->children()
                ->arrayNode('filter_eager_loading_extension')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('restricted_resources')
                            ->useAttributeAsKey('name')
                            ->arrayPrototype()
                                ->children()
                                    ->arrayNode('operations')
                                        ->useAttributeAsKey('name')
                                        ->arrayPrototype()
                                            ->canBeDisabled()
                                            ->children()
                                                ->booleanNode('enabled')->defaultTrue()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('jwt')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('firewall_expectations')
                            ->useAttributeAsKey('name')
                            ->defaultValue([
                                'new_api_admin_user' => ['audience' => 'sylius-api-admin', 'principal' => AdminUserInterface::class],
                                'new_api_shop_user' => ['audience' => 'sylius-api-shop', 'principal' => ShopUserInterface::class],
                            ])
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('audience')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode('principal')
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                        ->validate()
                                            ->ifTrue(static fn (string $principal): bool => !interface_exists($principal) && !class_exists($principal))
                                            ->thenInvalid('The principal "%s" is not an existing class or interface.')
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
