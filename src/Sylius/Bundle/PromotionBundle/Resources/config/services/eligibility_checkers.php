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

use Sylius\Component\Promotion\Checker\Eligibility\CompositePromotionCouponEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\CompositePromotionEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionArchivalEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponDurationEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponUsageLimitEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionDurationEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionRulesEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionSubjectCouponEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionUsageLimitEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\WithoutOwnAdjustmentsPromotionEligibilityChecker;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.checker.promotion_coupon.duration_eligibility', PromotionCouponDurationEligibilityChecker::class)
        ->private()
        ->tag('sylius.promotion_coupon_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.promotion_coupon.usage_limit_eligibility', PromotionCouponUsageLimitEligibilityChecker::class)
        ->private()
        ->tag('sylius.promotion_coupon_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.promotion_coupon_eligibility', CompositePromotionCouponEligibilityChecker::class)
        ->args([[]])
    ;
    $services->alias('sylius.checker.promotion_coupon_eligibility.composite', 'sylius.checker.promotion_coupon_eligibility');

    $services->alias(PromotionCouponEligibilityCheckerInterface::class, 'sylius.checker.promotion_coupon_eligibility');

    $services
        ->set('sylius.checker.promotion.duration_eligibility', PromotionDurationEligibilityChecker::class)
        ->private()
        ->tag('sylius.promotion_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.promotion.usage_limit_eligibility', PromotionUsageLimitEligibilityChecker::class)
        ->private()
        ->tag('sylius.promotion_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.promotion.subject_coupon_eligibility', PromotionSubjectCouponEligibilityChecker::class)
        ->args([service('sylius.checker.promotion_coupon_eligibility')])
        ->private()
        ->tag('sylius.promotion_eligibility_checker')
    ;

    $services
        ->set('sylius.checker.promotion.rules_eligibility', PromotionRulesEligibilityChecker::class)
        ->args([service('sylius.registry.promotion.rule_checker')])
        ->private()
        ->tag('sylius.promotion_eligibility_checker')
    ;

    $services->set('sylius.checker.promotion_eligibility', CompositePromotionEligibilityChecker::class)->args([[]]);
    $services->alias('sylius.checker.promotion_eligibility.composite', 'sylius.checker.promotion_eligibility');

    $services->alias(PromotionEligibilityCheckerInterface::class, 'sylius.checker.promotion_eligibility');

    $services
        ->set('sylius.checker.promotion_eligibility.without_own_adjustments', WithoutOwnAdjustmentsPromotionEligibilityChecker::class)
        ->args([
            service('sylius.checker.promotion_eligibility'),
            service('sylius.action.applicator.promotion'),
        ])
    ;

    $services
        ->set('sylius.checker.promotion.archival_eligibility', PromotionArchivalEligibilityChecker::class)
        ->private()
        ->tag('sylius.promotion_eligibility_checker')
    ;
};
