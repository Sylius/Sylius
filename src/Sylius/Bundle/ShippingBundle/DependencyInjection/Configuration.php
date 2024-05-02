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

namespace Sylius\Bundle\ShippingBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ShippingBundle\Form\Type\ShipmentType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingCategoryType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodRuleType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodTranslationType;
use Sylius\Bundle\ShippingBundle\Form\Type\ShippingMethodType;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Sylius\Component\Shipping\Model\Shipment;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShipmentUnit;
use Sylius\Component\Shipping\Model\ShipmentUnitInterface;
use Sylius\Component\Shipping\Model\ShippingCategory;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRule;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Sylius\Component\Shipping\Model\ShippingMethodTranslation;
use Sylius\Component\Shipping\Model\ShippingMethodTranslationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_shipping');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('shipping_method_rule')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('validation_groups')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('shipping_method_calculator')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('validation_groups')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('driver')->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)->end()
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
                        ->arrayNode('shipment')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/shipping-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Shipment::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShipmentInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ShipmentType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipment_unit')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/shipping-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShipmentUnit::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShipmentUnitInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_method')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/shipping-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingMethod::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingMethodInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(TranslatableFactory::class)->end()
                                        ->scalarNode('form')->defaultValue(ShippingMethodType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')
                                            ->setDeprecated('sylius/shipping-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                        ->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(ShippingMethodTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(ShippingMethodTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(ShippingMethodTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_method_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/shipping-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingMethodRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingMethodRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ShippingMethodRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('shipping_category')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/shipping-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(ShippingCategory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(ShippingCategoryInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(ShippingCategoryType::class)->cannotBeEmpty()->end()
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
