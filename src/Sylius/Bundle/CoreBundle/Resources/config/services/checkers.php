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

use Sylius\Component\Core\Checker\CLIContextChecker;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Sylius\Component\Core\Checker\Eligibility\PromotionCouponChannelEligibilityChecker;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderShippingMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Shipping\Checker\Eligibility\ZoneEligibilityChecker;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.checker.order_shipping_method_selection_requirement', OrderShippingMethodSelectionRequirementChecker::class)
        ->args([service('sylius.resolver.shipping_methods')]);

    $services->alias(OrderShippingMethodSelectionRequirementCheckerInterface::class, 'sylius.checker.order_shipping_method_selection_requirement');

    $services->set('sylius.checker.order_payment_method_selection_requirement', OrderPaymentMethodSelectionRequirementChecker::class)
        ->args([service('sylius.resolver.payment_methods')]);

    $services->alias(OrderPaymentMethodSelectionRequirementCheckerInterface::class, 'sylius.checker.order_payment_method_selection_requirement');

    $services->set('sylius.checker.cli_context', CLIContextChecker::class)
        ->args([service('request_stack')]);

    $services->alias(CLIContextCheckerInterface::class, 'sylius.checker.cli_context');

    $services->set('sylius.checker.promotion_coupon.channel_eligibility', PromotionCouponChannelEligibilityChecker::class)
        ->tag('sylius.promotion_coupon_eligibility_checker');

    $services->set('sylius.checker.shipping_method.zone_eligibility', ZoneEligibilityChecker::class)
        ->args([service('sylius.matcher.zone')])
        ->tag('sylius.shipping_method_eligibility_checker');
};
