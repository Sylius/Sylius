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

final readonly class Configuration implements ConfigurationInterface
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
                ->arrayNode('order_states_to_filter_out')
                    ->scalarPrototype()->end()
                ->end()
                ->arrayNode('operations_to_remove')
                    ->scalarPrototype()->end()
                ->end()
                ->variableNode('default_image_filter')
                    ->defaultValue('sylius_original')
                ->end()
                ->arrayNode('shop_payment_request')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('allowed_actions')
                            ->info('Payment request actions a shop-context caller is allowed to request. Actions such as "refund", "cancel" or "payout" are intentionally excluded.')
                            ->scalarPrototype()->end()
                            ->defaultValue(['capture', 'authorize', 'status', 'notify'])
                        ->end()
                    ->end()
                ->end()
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
                ->arrayNode('jwt')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('firewall_expectations')
                            ->info('Maps a firewall name to the JWT "aud" claim and the user principal interface expected on that firewall.')
                            ->useAttributeAsKey('name')
                            ->defaultValue([
                                'api_admin' => ['audience' => 'sylius-api-admin', 'principal' => AdminUserInterface::class],
                                'api_shop' => ['audience' => 'sylius-api-shop', 'principal' => ShopUserInterface::class],
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
