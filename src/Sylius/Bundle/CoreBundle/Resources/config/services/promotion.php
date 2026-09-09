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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\Promotion\Modifier\AtomicOrderPromotionsUsageModifier;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedFixedDiscountConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedPercentageDiscountConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedUnitFixedDiscountConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedUnitPercentageDiscountConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedCartQuantityConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedContainsProductConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedCustomerGroupConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedHasTaxonConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedItemTotalConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedNthOrderConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedShippingCountryConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedTotalOfItemsFromTaxonConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ContainsProductConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\CustomerGroupConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\HasTaxonConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\NthOrderConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ShippingCountryConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\Rule\CartQuantityConfigurationType;
use Sylius\Component\Core\Factory\PromotionActionFactory;
use Sylius\Component\Core\Factory\PromotionActionFactoryInterface;
use Sylius\Component\Core\Factory\PromotionRuleFactory;
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\PerChannelPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicator;
use Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Sylius\Component\Core\Promotion\Checker\Eligibility\PromotionCouponPerCustomerUsageLimitEligibilityChecker;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleChecker;
use Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\CustomerGroupRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\PerChannelRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\ShippingCountryRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleChecker;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Sylius\Component\Core\Promotion\Filter\PriceRangeFilter;
use Sylius\Component\Core\Promotion\Filter\ProductFilter;
use Sylius\Component\Core\Promotion\Filter\TaxonFilter;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifier;
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\HasTaxonRuleUpdater;
use Sylius\Component\Core\Provider\ActivePromotionsByChannelProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.custom_factory.promotion_action', PromotionActionFactory::class)
        ->decorate('sylius.factory.promotion_action', null, 256)
        ->args([service('sylius.custom_factory.promotion_action.inner')])
    ;
    $services->alias(PromotionActionFactoryInterface::class, 'sylius.custom_factory.promotion_action');

    $services
        ->set('sylius.custom_factory.promotion_rule', PromotionRuleFactory::class)
        ->decorate('sylius.factory.promotion_rule', null, 256)
        ->args([service('sylius.custom_factory.promotion_rule.inner')])
    ;
    $services->alias(PromotionRuleFactoryInterface::class, 'sylius.custom_factory.promotion_rule');

    $services
        ->set('sylius.provider.active_promotions', ActivePromotionsByChannelProvider::class)
        ->args([service('sylius.repository.promotion')])
    ;

    $services
        ->set('sylius.checker.promotion_rule.customer_group', CustomerGroupRuleChecker::class)
        ->tag('sylius.promotion_rule_checker', ['type' => 'customer_group', 'label' => 'sylius.form.promotion_rule.customer_group', 'form_type' => CustomerGroupConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.customer_group_per_channel', PerChannelRuleChecker::class)
        ->args([service('sylius.checker.promotion_rule.customer_group')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'customer_group_per_channel', 'label' => 'sylius.form.promotion_rule.customer_group_per_channel', 'form_type' => ChannelBasedCustomerGroupConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.nth_order', NthOrderRuleChecker::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'nth_order', 'label' => 'sylius.form.promotion_rule.nth_order', 'form_type' => NthOrderConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.nth_order_per_channel', PerChannelRuleChecker::class)
        ->args([service('sylius.checker.promotion_rule.nth_order')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'nth_order_per_channel', 'label' => 'sylius.form.promotion_rule.nth_order_per_channel', 'form_type' => ChannelBasedNthOrderConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.shipping_country', ShippingCountryRuleChecker::class)
        ->args([service('sylius.repository.country')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'shipping_country', 'label' => 'sylius.form.promotion_rule.shipping_country', 'form_type' => ShippingCountryConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.shipping_country_per_channel', PerChannelRuleChecker::class)
        ->args([service('sylius.checker.promotion_rule.shipping_country')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'shipping_country_per_channel', 'label' => 'sylius.form.promotion_rule.shipping_country_per_channel', 'form_type' => ChannelBasedShippingCountryConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.has_taxon', HasTaxonRuleChecker::class)
        ->tag('sylius.promotion_rule_checker', ['type' => 'has_taxon', 'label' => 'sylius.form.promotion_rule.has_at_least_one_from_taxons', 'form_type' => HasTaxonConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.has_taxon_per_channel', PerChannelRuleChecker::class)
        ->args([service('sylius.checker.promotion_rule.has_taxon')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'has_taxon_per_channel', 'label' => 'sylius.form.promotion_rule.has_at_least_one_from_taxons_per_channel', 'form_type' => ChannelBasedHasTaxonConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.total_of_items_from_taxon', TotalOfItemsFromTaxonRuleChecker::class)
        ->args([service('sylius.repository.taxon')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'total_of_items_from_taxon', 'label' => 'sylius.form.promotion_rule.total_price_of_items_from_taxon', 'form_type' => ChannelBasedTotalOfItemsFromTaxonConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.contains_product', ContainsProductRuleChecker::class)
        ->tag('sylius.promotion_rule_checker', ['type' => 'contains_product', 'label' => 'sylius.form.promotion_rule.contains_product', 'form_type' => ContainsProductConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.contains_product_per_channel', PerChannelRuleChecker::class)
        ->args([service('sylius.checker.promotion_rule.contains_product')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'contains_product_per_channel', 'label' => 'sylius.form.promotion_rule.contains_product_per_channel', 'form_type' => ChannelBasedContainsProductConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.item_total', ItemTotalRuleChecker::class)
        ->args([service('sylius.promotion.comparison_operator_matcher')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'item_total', 'label' => 'sylius.form.promotion_rule.item_total', 'form_type' => ChannelBasedItemTotalConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.cart_quantity', CartQuantityRuleChecker::class)
        ->args([service('sylius.promotion.comparison_operator_matcher')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'cart_quantity', 'label' => 'sylius.form.promotion_rule.cart_quantity', 'form_type' => CartQuantityConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion_rule.cart_quantity_per_channel', PerChannelRuleChecker::class)
        ->args([service('sylius.checker.promotion_rule.cart_quantity')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'cart_quantity_per_channel', 'label' => 'sylius.form.promotion_rule.cart_quantity_per_channel', 'form_type' => ChannelBasedCartQuantityConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.fixed_discount', FixedDiscountPromotionActionCommand::class)
        ->args([
            service('sylius.distributor.proportional_integer'),
            service('sylius.applicator.promotion.units_adjustments'),
            service('sylius.distributor.minimum_price'),
            service('sylius.converter.currency'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'order_fixed_discount', 'label' => 'sylius.form.promotion_action.order_fixed_discount', 'form_type' => ChannelBasedFixedDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.unit_fixed_discount', UnitFixedDiscountPromotionActionCommand::class)
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.filter.promotion.price_range'),
            service('sylius.filter.promotion.taxon'),
            service('sylius.filter.promotion.product'),
            service('sylius.converter.currency'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'unit_fixed_discount', 'label' => 'sylius.form.promotion_action.item_fixed_discount', 'form_type' => ChannelBasedUnitFixedDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.percentage_discount', PercentageDiscountPromotionActionCommand::class)
        ->args([
            service('sylius.distributor.proportional_integer'),
            service('sylius.applicator.promotion.units_adjustments'),
            service('sylius.distributor.minimum_price'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'order_percentage_discount', 'label' => 'sylius.form.promotion_action.order_percentage_discount', 'form_type' => PercentageDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.percentage_discount_per_channel', PerChannelPromotionActionCommand::class)
        ->args([service('sylius.command.promotion_action.percentage_discount')])
        ->tag('sylius.promotion_action', ['type' => 'order_percentage_discount_per_channel', 'label' => 'sylius.form.promotion_action.order_percentage_discount_per_channel', 'form_type' => ChannelBasedPercentageDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.unit_percentage_discount', UnitPercentageDiscountPromotionActionCommand::class)
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.filter.promotion.price_range'),
            service('sylius.filter.promotion.taxon'),
            service('sylius.filter.promotion.product'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'unit_percentage_discount', 'label' => 'sylius.form.promotion_action.item_percentage_discount', 'form_type' => ChannelBasedUnitPercentageDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.shipping_percentage_discount', ShippingPercentageDiscountPromotionActionCommand::class)
        ->args([service('sylius.factory.adjustment')])
        ->tag('sylius.promotion_action', ['type' => 'shipping_percentage_discount', 'label' => 'sylius.form.promotion_action.shipping_percentage_discount', 'form_type' => PercentageDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.command.promotion_action.shipping_percentage_discount_per_channel', PerChannelPromotionActionCommand::class)
        ->args([service('sylius.command.promotion_action.shipping_percentage_discount')])
        ->tag('sylius.promotion_action', ['type' => 'shipping_percentage_discount_per_channel', 'label' => 'sylius.form.promotion_action.shipping_percentage_discount_per_channel', 'form_type' => ChannelBasedPercentageDiscountConfigurationType::class])
    ;

    $services
        ->set('sylius.checker.promotion.promotion_coupon_per_customer_usage_limit_eligibility', PromotionCouponPerCustomerUsageLimitEligibilityChecker::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.promotion_coupon_eligibility_checker')
    ;

    $services->set('sylius.filter.promotion.taxon', TaxonFilter::class);

    $services->set('sylius.filter.promotion.product', ProductFilter::class);

    $services
        ->set('sylius.filter.promotion.price_range', PriceRangeFilter::class)
        ->args([service('sylius.calculator.product_variant_price')])
    ;

    $services
        ->set('sylius.applicator.promotion.units_adjustments', UnitsPromotionAdjustmentsApplicator::class)
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.distributor.integer'),
        ])
    ;
    $services->alias(UnitsPromotionAdjustmentsApplicatorInterface::class, 'sylius.applicator.promotion.units_adjustments');

    $services
        ->set('sylius.modifier.promotion.order_usage', OrderPromotionsUsageModifier::class)
        ->public()
    ;
    $services->alias(OrderPromotionsUsageModifierInterface::class, 'sylius.modifier.promotion.order_usage')->public();

    $services
        ->set('sylius.modifier.promotion.order_usage.atomic', AtomicOrderPromotionsUsageModifier::class)
        ->decorate('sylius.modifier.promotion.order_usage')
        ->args([
            null,
            service('.inner'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.updater.promotion_rule.has_taxon', HasTaxonRuleUpdater::class)
        ->args([
            service('sylius.repository.promotion_rule'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.checker.promotion.product_in_promotion_rule', ProductInPromotionRuleChecker::class)
        ->args([service('sylius.repository.promotion_rule')])
    ;
    $services->alias(ProductInPromotionRuleCheckerInterface::class, 'sylius.checker.promotion.product_in_promotion_rule');

    $services
        ->set('sylius.checker.promotion.taxon_in_promotion_rule', TaxonInPromotionRuleChecker::class)
        ->args([service('sylius.repository.promotion_rule')])
    ;
    $services->alias(TaxonInPromotionRuleCheckerInterface::class, 'sylius.checker.promotion.taxon_in_promotion_rule');
};
