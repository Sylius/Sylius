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

use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionGroupValidator;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionActionTypeValidator;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeGroupValidator;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionScopeTypeValidator;
use Sylius\Bundle\PromotionBundle\Validator\CouponGenerationAmountValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionActionGroupValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionActionTypeValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionDateRangeValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionNotCouponBasedValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionRuleGroupValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionRuleTypeValidator;
use Sylius\Bundle\PromotionBundle\Validator\PromotionSubjectCouponValidator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.validator.promotion_subject_coupon', PromotionSubjectCouponValidator::class)
        ->args([service('sylius.checker.promotion_eligibility')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_subject_validator'])
    ;

    $services
        ->set('sylius.validator.promotion_date_range', PromotionDateRangeValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_date_range_validator'])
    ;

    $services
        ->set('sylius.validator.promotion_coupon_generation_amount', CouponGenerationAmountValidator::class)
        ->args([service('sylius.generator.percentage_generation_policy')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_coupon_generation_amount_validator'])
    ;

    $services
        ->set('sylius.validator.catalog_promotion_action_group', CatalogPromotionActionGroupValidator::class)
        ->args(['%sylius.promotion.catalog_promotion_action.validation_groups%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_catalog_promotion_action_group'])
    ;

    $services
        ->set('sylius.validator.catalog_promotion_action_type', CatalogPromotionActionTypeValidator::class)
        ->args(['%sylius.catalog_promotion.actions_types%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_catalog_promotion_action_type_validator'])
    ;

    $services
        ->set('sylius.validator.catalog_promotion_scope_group', CatalogPromotionScopeGroupValidator::class)
        ->args(['%sylius.promotion.catalog_promotion_scope.validation_groups%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_catalog_promotion_scope_group'])
    ;

    $services
        ->set('sylius.validator.catalog_promotion_scope_type', CatalogPromotionScopeTypeValidator::class)
        ->args(['%sylius.catalog_promotion.scopes_types%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_catalog_promotion_scope_type_validator'])
    ;

    $services
        ->set('sylius.validator.promotion_action_group', PromotionActionGroupValidator::class)
        ->args(['%sylius.promotion.promotion_action.validation_groups%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_action_group'])
    ;

    $services
        ->set('sylius.validator.promotion_action_type', PromotionActionTypeValidator::class)
        ->args(['%sylius.promotion_actions%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_action_type'])
    ;

    $services
        ->set('sylius.validator.promotion_role_group', PromotionRuleGroupValidator::class)
        ->args(['%sylius.promotion.promotion_rule.validation_groups%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_rule_group'])
    ;

    $services
        ->set('sylius.validator.promotion_role_type', PromotionRuleTypeValidator::class)
        ->args(['%sylius.promotion_rules%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_rule_type'])
    ;

    $services
        ->set('sylius.validator.promotion_not_coupon_based', PromotionNotCouponBasedValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_not_coupon_based_validator'])
    ;
};
