<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.custom_factory.promotion_action', 'Sylius\Component\Core\Factory\PromotionActionFactory')
        ->private()
        ->decorate('sylius.factory.promotion_action', null, 256)
        ->args([service('sylius.custom_factory.promotion_action.inner')]);

    $services->alias('Sylius\Component\Core\Factory\PromotionActionFactoryInterface', 'sylius.custom_factory.promotion_action');

    $services->set('sylius.custom_factory.promotion_rule', 'Sylius\Component\Core\Factory\PromotionRuleFactory')
        ->private()
        ->decorate('sylius.factory.promotion_rule', null, 256)
        ->args([service('sylius.custom_factory.promotion_rule.inner')]);

    $services->alias('Sylius\Component\Core\Factory\PromotionRuleFactoryInterface', 'sylius.custom_factory.promotion_rule');

    $services->set('sylius.provider.active_promotions', 'Sylius\Component\Core\Provider\ActivePromotionsByChannelProvider')
        ->args([service('sylius.repository.promotion')]);

    $services->set('sylius.checker.promotion_rule.customer_group', 'Sylius\Component\Core\Promotion\Checker\Rule\CustomerGroupRuleChecker')
        ->private()
        ->tag('sylius.promotion_rule_checker', ['type' => 'customer_group', 'label' => 'sylius.form.promotion_rule.customer_group', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\CustomerGroupConfigurationType']);

    $services->set('sylius.checker.promotion_rule.nth_order', 'Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'nth_order', 'label' => 'sylius.form.promotion_rule.nth_order', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\NthOrderConfigurationType']);

    $services->set('sylius.checker.promotion_rule.shipping_country', 'Sylius\Component\Core\Promotion\Checker\Rule\ShippingCountryRuleChecker')
        ->args([service('sylius.repository.country')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'shipping_country', 'label' => 'sylius.form.promotion_rule.shipping_country', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ShippingCountryConfigurationType']);

    $services->set('sylius.checker.promotion_rule.has_taxon', 'Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker')
        ->tag('sylius.promotion_rule_checker', ['type' => 'has_taxon', 'label' => 'sylius.form.promotion_rule.has_at_least_one_from_taxons', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\HasTaxonConfigurationType']);

    $services->set('sylius.checker.promotion_rule.total_of_items_from_taxon', 'Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker')
        ->args([service('sylius.repository.taxon')])
        ->tag('sylius.promotion_rule_checker', ['type' => 'total_of_items_from_taxon', 'label' => 'sylius.form.promotion_rule.total_price_of_items_from_taxon', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedTotalOfItemsFromTaxonConfigurationType']);

    $services->set('sylius.checker.promotion_rule.contains_product', 'Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker')
        ->tag('sylius.promotion_rule_checker', ['type' => 'contains_product', 'label' => 'sylius.form.promotion_rule.contains_product', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ContainsProductConfigurationType']);

    $services->set('sylius.checker.promotion_rule.item_total', 'Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker')
        ->tag('sylius.promotion_rule_checker', ['type' => 'item_total', 'label' => 'sylius.form.promotion_rule.item_total', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ChannelBasedItemTotalConfigurationType']);

    $services->set('sylius.checker.promotion_rule.cart_quantity', 'Sylius\Component\Core\Promotion\Checker\Rule\CartQuantityRuleChecker')
        ->tag('sylius.promotion_rule_checker', ['type' => 'cart_quantity', 'label' => 'sylius.form.promotion_rule.cart_quantity', 'form_type' => 'Sylius\Bundle\PromotionBundle\Form\Type\Rule\CartQuantityConfigurationType']);

    $services->set('sylius.command.promotion_action.fixed_discount', 'Sylius\Component\Core\Promotion\Action\FixedDiscountPromotionActionCommand')
        ->args([
            service('sylius.distributor.proportional_integer'),
            service('sylius.applicator.promotion.units_adjustments'),
            service('sylius.distributor.minimum_price'),
            service('sylius.converter.currency'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'order_fixed_discount', 'label' => 'sylius.form.promotion_action.order_fixed_discount', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedFixedDiscountConfigurationType']);

    $services->set('sylius.command.promotion_action.unit_fixed_discount', 'Sylius\Component\Core\Promotion\Action\UnitFixedDiscountPromotionActionCommand')
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.filter.promotion.price_range'),
            service('sylius.filter.promotion.taxon'),
            service('sylius.filter.promotion.product'),
            service('sylius.converter.currency'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'unit_fixed_discount', 'label' => 'sylius.form.promotion_action.item_fixed_discount', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedUnitFixedDiscountConfigurationType']);

    $services->set('sylius.command.promotion_action.percentage_discount', 'Sylius\Component\Core\Promotion\Action\PercentageDiscountPromotionActionCommand')
        ->args([
            service('sylius.distributor.proportional_integer'),
            service('sylius.applicator.promotion.units_adjustments'),
            service('sylius.distributor.minimum_price'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'order_percentage_discount', 'label' => 'sylius.form.promotion_action.order_percentage_discount', 'form_type' => 'Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType']);

    $services->set('sylius.command.promotion_action.unit_percentage_discount', 'Sylius\Component\Core\Promotion\Action\UnitPercentageDiscountPromotionActionCommand')
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.filter.promotion.price_range'),
            service('sylius.filter.promotion.taxon'),
            service('sylius.filter.promotion.product'),
        ])
        ->tag('sylius.promotion_action', ['type' => 'unit_percentage_discount', 'label' => 'sylius.form.promotion_action.item_percentage_discount', 'form_type' => 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Action\ChannelBasedUnitPercentageDiscountConfigurationType']);

    $services->set('sylius.command.promotion_action.shipping_percentage_discount', 'Sylius\Component\Core\Promotion\Action\ShippingPercentageDiscountPromotionActionCommand')
        ->args([service('sylius.factory.adjustment')])
        ->tag('sylius.promotion_action', ['type' => 'shipping_percentage_discount', 'label' => 'sylius.form.promotion_action.shipping_percentage_discount', 'form_type' => 'Sylius\Bundle\PromotionBundle\Form\Type\Action\PercentageDiscountConfigurationType']);

    $services->set('sylius.checker.promotion.promotion_coupon_per_customer_usage_limit_eligibility', 'Sylius\Component\Core\Promotion\Checker\Eligibility\PromotionCouponPerCustomerUsageLimitEligibilityChecker')
        ->private()
        ->args([service('sylius.repository.order')])
        ->tag('sylius.promotion_coupon_eligibility_checker');

    $services->set('sylius.filter.promotion.taxon', 'Sylius\Component\Core\Promotion\Filter\TaxonFilter');

    $services->set('sylius.filter.promotion.product', 'Sylius\Component\Core\Promotion\Filter\ProductFilter');

    $services->set('sylius.filter.promotion.price_range', 'Sylius\Component\Core\Promotion\Filter\PriceRangeFilter')
        ->args([service('sylius.calculator.product_variant_price')]);

    $services->set('sylius.applicator.promotion.units_adjustments', 'Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicator')
        ->args([
            service('sylius.factory.adjustment'),
            service('sylius.distributor.integer'),
        ]);

    $services->alias('Sylius\Component\Core\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface', 'sylius.applicator.promotion.units_adjustments');

    $services->set('sylius.modifier.promotion.order_usage', 'Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifier')
        ->public()
        ->args([service('sylius.manager.promotion')]);

    $services->alias('Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface', 'sylius.modifier.promotion.order_usage')
        ->public();

    $services->set('sylius.updater.promotion_rule.has_taxon', 'Sylius\Component\Core\Promotion\Updater\Rule\HasTaxonRuleUpdater')
        ->args([
            service('sylius.repository.promotion_rule'),
            service('doctrine.orm.entity_manager'),
        ]);

    $services->set('sylius.checker.promotion.product_in_promotion_rule', 'Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleChecker')
        ->args([service('sylius.repository.promotion_rule')]);

    $services->alias('Sylius\Component\Core\Promotion\Checker\ProductInPromotionRuleCheckerInterface', 'sylius.checker.promotion.product_in_promotion_rule');

    $services->set('sylius.checker.promotion.taxon_in_promotion_rule', 'Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleChecker')
        ->args([service('sylius.repository.promotion_rule')]);

    $services->alias('Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface', 'sylius.checker.promotion.taxon_in_promotion_rule');
};
