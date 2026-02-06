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

use Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponGeneratorInstructionType;
use Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponType;
use Sylius\Bundle\AdminBundle\Twig\Component\PromotionCoupon\GeneratorInstructionFormComponent;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.promotion_coupon.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.promotion_coupon'),
            service('form.factory'),
            '%sylius.model.promotion_coupon.class%',
            PromotionCouponType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion_coupon:form']);

    $services->set('sylius_admin.twig.component.promotion_coupon.generator_instruction_form', GeneratorInstructionFormComponent::class)
        ->args([
            service('form.factory'),
            PromotionCouponGeneratorInstructionType::class,
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion_coupon:generator_instruction_form']);
};
