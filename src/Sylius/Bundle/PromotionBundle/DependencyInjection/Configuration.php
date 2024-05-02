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

namespace Sylius\Bundle\PromotionBundle\DependencyInjection;

use Sylius\Bundle\PromotionBundle\Controller\PromotionCouponController;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionTranslationType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionTranslationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\Form\Type\DefaultResourceType;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Promotion\Model\CatalogPromotion;
use Sylius\Component\Promotion\Model\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScope;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslation;
use Sylius\Component\Promotion\Model\CatalogPromotionTranslationInterface;
use Sylius\Component\Promotion\Model\Promotion;
use Sylius\Component\Promotion\Model\PromotionAction;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionCoupon;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRule;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Model\PromotionTranslation;
use Sylius\Component\Promotion\Model\PromotionTranslationInterface;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('sylius_promotion');
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('promotion_action')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('validation_groups')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('promotion_rule')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('validation_groups')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('catalog_promotion_action')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('validation_groups')
                            ->useAttributeAsKey('name')
                            ->variablePrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('catalog_promotion_scope')
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
                    ->isRequired()
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('promotion_subject')
                            ->isRequired()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->isRequired()
                                    ->children()
                                        ->scalarNode('model')->isRequired()->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Promotion::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PromotionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(PromotionType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')
                                            ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                        ->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(PromotionTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(PromotionTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(PromotionTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('catalog_promotion')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CatalogPromotion::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CatalogPromotionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(CatalogPromotionType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->arrayNode('translation')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->variableNode('options')
                                            ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                        ->end()
                                        ->arrayNode('classes')
                                            ->addDefaultsIfNotSet()
                                            ->children()
                                                ->scalarNode('model')->defaultValue(CatalogPromotionTranslation::class)->cannotBeEmpty()->end()
                                                ->scalarNode('interface')->defaultValue(CatalogPromotionTranslationInterface::class)->cannotBeEmpty()->end()
                                                ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                                ->scalarNode('repository')->cannotBeEmpty()->end()
                                                ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                                ->scalarNode('form')->defaultValue(CatalogPromotionTranslationType::class)->cannotBeEmpty()->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('catalog_promotion_scope')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CatalogPromotionScope::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CatalogPromotionScopeInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('catalog_promotion_action')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(CatalogPromotionAction::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(CatalogPromotionActionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(DefaultResourceType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_rule')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PromotionRule::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PromotionRuleInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(PromotionRuleType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_action')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PromotionAction::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PromotionActionInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(PromotionActionType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('promotion_coupon')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')
                                    ->setDeprecated('sylius/promotion-bundle', '1.13', 'The "%node%" node at "%path%" is deprecated and will be removed in 2.0.')
                                ->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PromotionCoupon::class)->cannotBeEmpty()->end()
                                        ->scalarNode('interface')->defaultValue(PromotionCouponInterface::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(PromotionCouponController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                        ->scalarNode('form')->defaultValue(PromotionCouponType::class)->cannotBeEmpty()->end()
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
