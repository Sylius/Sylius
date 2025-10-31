<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.checker.order_shipping_method_selection_requirement', 'Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementChecker')
        ->args([service('sylius.resolver.shipping_methods')]);

    $services->alias('Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface', 'sylius.checker.order_shipping_method_selection_requirement');

    $services->set('sylius.checker.order_payment_method_selection_requirement', 'Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker')
        ->args([service('sylius.resolver.payment_methods')]);

    $services->alias('Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface', 'sylius.checker.order_payment_method_selection_requirement');

    $services->set('sylius.checker.cli_context', 'Sylius\Component\Core\Checker\CLIContextChecker')
        ->args([service('request_stack')]);

    $services->alias('Sylius\Component\Core\Checker\CLIContextCheckerInterface', 'sylius.checker.cli_context');

    $services->set('sylius.checker.promotion_coupon.channel_eligibility', 'Sylius\Component\Core\Checker\Eligibility\PromotionCouponChannelEligibilityChecker')
        ->private()
        ->tag('sylius.promotion_coupon_eligibility_checker');

    $services->set('sylius.checker.shipping_method.zone_eligibility', 'Sylius\Component\Core\Shipping\Checker\Eligibility\ZoneEligibilityChecker')
        ->args([service('sylius.matcher.zone')])
        ->tag('sylius.shipping_method_eligibility_checker');
};
