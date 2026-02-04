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

use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProvider;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistry;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Action\PromotionApplicator;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Factory\PromotionCouponFactory;
use Sylius\Component\Promotion\Factory\PromotionCouponFactoryInterface;
use Sylius\Component\Promotion\Factory\PromotionCouponGeneratorInstructionFactory;
use Sylius\Component\Promotion\Factory\PromotionCouponGeneratorInstructionFactoryInterface;
use Sylius\Component\Promotion\Generator\PercentageGenerationPolicy;
use Sylius\Component\Promotion\Generator\PromotionCouponGenerator;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessor;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\Provider\ActivePromotionsProvider;
use Sylius\Component\Registry\ServiceRegistry;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('services/*.php');

    $services->set('sylius.custom_factory.promotion_coupon', PromotionCouponFactory::class)
        ->decorate('sylius.factory.promotion_coupon', null, 256)
        ->args([service('sylius.custom_factory.promotion_coupon.inner')]);

    $services->alias(PromotionCouponFactoryInterface::class, 'sylius.factory.promotion_coupon');

    $services->set('sylius.factory.promotion_coupon_generator_instruction', PromotionCouponGeneratorInstructionFactory::class);

    $services->alias(PromotionCouponGeneratorInstructionFactoryInterface::class, 'sylius.factory.promotion_coupon_generator_instruction');

    $services->set('sylius.processor.promotion', PromotionProcessor::class)
        ->args([
            service('sylius.provider.active_promotions'),
            service('sylius.checker.promotion_eligibility'),
            service('sylius.action.applicator.promotion'),
        ]);

    $services->alias(PromotionProcessorInterface::class, 'sylius.processor.promotion');

    $services->set('sylius.action.applicator.promotion', PromotionApplicator::class)
        ->args([service('sylius.registry.promotion_action')]);

    $services->alias(PromotionApplicatorInterface::class, 'sylius.action.applicator.promotion');

    $services->set('sylius.registry.promotion.rule_checker', ServiceRegistry::class)
        ->args([
            RuleCheckerInterface::class,
            'rule checker',
        ]);

    $services->set('sylius.form_registry.promotion_rule_checker', FormTypeRegistry::class);

    $services->set('sylius.registry.promotion_action', ServiceRegistry::class)
        ->args([
            PromotionActionCommandInterface::class,
            'promotion action',
        ]);

    $services->set('sylius.form_registry.promotion_action', FormTypeRegistry::class);

    $services->set('sylius.provider.active_promotions', ActivePromotionsProvider::class)
        ->args([service('sylius.repository.promotion')]);

    $services->alias(ActivePromotionsProvider::class, 'sylius.provider.active_promotions');

    $services->set('sylius.generator.promotion_coupon', PromotionCouponGenerator::class)
        ->public()
        ->args([
            service('sylius.factory.promotion_coupon'),
            service('sylius.repository.promotion_coupon'),
            service('sylius.manager.promotion_coupon'),
            service('sylius.generator.percentage_generation_policy'),
        ]);

    $services->alias(PromotionCouponGeneratorInterface::class, 'sylius.generator.promotion_coupon')
        ->public();

    $services->set('sylius.generator.percentage_generation_policy', PercentageGenerationPolicy::class)
        ->args([service('sylius.repository.promotion_coupon')]);

    $services->set('sylius.provider.eligible_catalog_promotions', EligibleCatalogPromotionsProvider::class)
        ->public()
        ->args([
            service('sylius.repository.catalog_promotion'),
            tagged_iterator('sylius.catalog_promotion.criteria'),
        ]);

    $services->alias(EligibleCatalogPromotionsProviderInterface::class, 'sylius.provider.eligible_catalog_promotions')
        ->public();
};
