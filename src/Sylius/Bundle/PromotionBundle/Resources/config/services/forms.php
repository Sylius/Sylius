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

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionScopeType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionTranslationType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionChoiceType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponGeneratorInstructionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponToCodeType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleChoiceType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionTranslationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionType;
use Sylius\Bundle\PromotionBundle\Form\Type\Rule\CartQuantityConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\Rule\ItemTotalConfigurationType;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInstruction;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius.form.type.catalog_promotion.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.catalog_promotion_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.promotion.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.promotion_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.promotion_action.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.promotion_scope.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.promotion_coupon.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.promotion_coupon_generator_instruction.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.type.catalog_promotion', CatalogPromotionType::class)
        ->args([
            '%sylius.model.catalog_promotion.class%',
            '%sylius.form.type.catalog_promotion.validation_groups%',
            CatalogPromotionTranslationType::class,
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_scope', CatalogPromotionScopeType::class)
        ->args([
            '%sylius.model.catalog_promotion_scope.class%',
            '%sylius.form.type.catalog_promotion.validation_groups%',
            tagged_iterator('sylius.catalog_promotion.scope_configuration_type', indexAttribute: 'key'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_action', CatalogPromotionActionType::class)
        ->args([
            '%sylius.model.catalog_promotion_action.class%',
            '%sylius.form.type.catalog_promotion.validation_groups%',
            tagged_iterator('sylius.catalog_promotion.action_configuration_type', indexAttribute: 'key'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_translation', CatalogPromotionTranslationType::class)
        ->args([
            '%sylius.model.catalog_promotion_translation.class%',
            '%sylius.form.type.catalog_promotion_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion', PromotionType::class)
        ->args([
            '%sylius.model.promotion.class%',
            '%sylius.form.type.promotion.validation_groups%',
            PromotionTranslationType::class,
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_translation', PromotionTranslationType::class)
        ->args([
            '%sylius.model.promotion_translation.class%',
            '%sylius.form.type.promotion_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_action', PromotionActionType::class)
        ->args([
            '%sylius.model.promotion_action.class%',
            '%sylius.form.type.promotion_action.validation_groups%',
            service('sylius.form_registry.promotion_action'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule.cart_quantity_configuration', CartQuantityConfigurationType::class)
        ->args([service('sylius.promotion.comparison_operator_matcher')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule.item_total_configuration', ItemTotalConfigurationType::class)
        ->args([service('sylius.promotion.comparison_operator_matcher')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule', PromotionRuleType::class)
        ->args([
            '%sylius.model.promotion_rule.class%',
            '%sylius.form.type.promotion_scope.validation_groups%',
            service('sylius.form_registry.promotion_rule_checker'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_coupon', PromotionCouponType::class)
        ->args([
            '%sylius.model.promotion_coupon.class%',
            '%sylius.form.type.promotion_coupon.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_action_collection', PromotionActionCollectionType::class)
        ->args([service('sylius.registry.promotion_action')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule_collection', PromotionRuleCollectionType::class)
        ->args([service('sylius.registry.promotion.rule_checker')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_action_choice', PromotionActionChoiceType::class)
        ->args(['%sylius.promotion_actions%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule_choice', PromotionRuleChoiceType::class)
        ->args(['%sylius.promotion_rules%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_coupon_to_code', PromotionCouponToCodeType::class)
        ->args([service('sylius.repository.promotion_coupon')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_coupon_generator_instruction', PromotionCouponGeneratorInstructionType::class)
        ->args([
            inline_service(DataMapper::class),
            service('sylius.factory.promotion_coupon_generator_instruction'),
            PromotionCouponGeneratorInstruction::class,
            '%sylius.form.type.promotion_coupon_generator_instruction.validation_groups%',
        ])
        ->tag('form.type')
    ;
};
