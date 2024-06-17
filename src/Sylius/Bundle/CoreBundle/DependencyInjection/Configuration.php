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

namespace Sylius\Bundle\CoreBundle\DependencyInjection;

use Sylius\Bundle\CoreBundle\Controller\ProductTaxonController;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\ChannelPricingLogEntryRepository;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\CoreBundle\Form\Type\ShopBillingDataType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Core\Factory\ChannelPricingLogEntryFactory;
use Sylius\Component\Core\Model\AvatarImage;
use Sylius\Component\Core\Model\AvatarImageInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfig;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntry;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Core\Model\ProductImage;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductTaxon;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Sylius\Component\Core\Model\ShopBillingData;
use Sylius\Component\Core\Model\ShopBillingDataInterface;
use Sylius\Component\Core\Model\TaxonImage;
use Sylius\Component\Core\Model\TaxonImageInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_core');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
                ->scalarNode('autoconfigure_with_attributes')->defaultFalse()->end()
                ->booleanNode('prepend_doctrine_migrations')->defaultTrue()->end()
                ->booleanNode('shipping_address_based_taxation')->defaultFalse()->end()
                ->booleanNode('order_by_identifier')->defaultTrue()->end()
                ->booleanNode('process_shipments_before_recalculating_prices')
                    ->setDeprecated('sylius/sylius', '1.10', 'The "%path%.%node%" parameter is deprecated and will be removed in 2.0.')
                    ->defaultFalse()
                ->end()
                ->integerNode('order_token_length')
                    ->defaultValue(64)
                    ->min(1)->max(255)
                ->end()
                ->integerNode('max_int_value')->defaultValue(2147483647)->end()
                ->arrayNode('orders_statistics')
                    ->children()
                        ->arrayNode('intervals_map')
                            ->useAttributeAsKey('type')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('interval')
                                        ->cannotBeEmpty()
                                        ->validate()
                                            ->ifTrue(
                                                function (mixed $interval) {
                                                    try {
                                                        new \DateInterval($interval);

                                                        return false;
                                                    } catch (\Exception $e) {
                                                        return true;
                                                    }
                                                },
                                            )
                                            ->thenInvalid('Invalid format for interval "%s". Expected a string compatible with DateInterval.')
                                        ->end()
                                    ->end()
                                    ->scalarNode('period_format')->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('catalog_promotions')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('batch_size')
                            ->defaultValue(100)
                            ->validate()
                                ->ifTrue(fn (int $batchSize): bool => $batchSize <= 0)
                                ->thenInvalid('Expected value bigger than 0, but got %s.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('price_history')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('batch_size')
                            ->defaultValue(100)
                            ->validate()
                                ->ifTrue(fn (int $batchSize): bool => $batchSize <= 0)
                                ->thenInvalid('Expected value bigger than 0, but got %s.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('filesystem')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('adapter')
                            ->defaultValue('default')
                            ->validate()
                                ->ifNotInArray(['default', 'flysystem', 'gaufrette'])
                                ->thenInvalid('Expected adapter "default", "flysystem" or "gaufrette", but %s passed.')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('state_machine')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('default_adapter')->defaultValue('winzou_state_machine')->end()
                        ->arrayNode('graphs_to_adapters_mapping')
                            ->useAttributeAsKey('graph_name')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
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
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('product_image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductImage::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductImageInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('avatar_image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(AvatarImage::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(AvatarImageInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('taxon_image')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(TaxonImage::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(TaxonImageInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('product_taxon')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ProductTaxon::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ProductTaxonInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ProductTaxonController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('channel_pricing')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ChannelPricing::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ChannelPricingInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ChannelPricingType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('channel_pricing_log_entry')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ChannelPricingLogEntry::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ChannelPricingLogEntryInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(ChannelPricingLogEntryRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(ChannelPricingLogEntryFactory::class)->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shop_billing_data')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShopBillingData::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShopBillingDataInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(ShopBillingDataType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('channel_price_history_config')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/core-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ChannelPriceHistoryConfig::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ChannelPriceHistoryConfigInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
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
