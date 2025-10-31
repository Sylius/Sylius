<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.promotion_coupon.form', 'Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent')
        ->args([
            service('sylius.repository.promotion_coupon'),
            service('form.factory'),
            '%sylius.model.promotion_coupon.class%',
            'Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion_coupon:form']);

    $services->set('sylius_admin.twig.component.promotion_coupon.generator_instruction_form', 'Sylius\Bundle\AdminBundle\Twig\Component\PromotionCoupon\GeneratorInstructionFormComponent')
        ->args([
            service('form.factory'),
            '\Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponGeneratorInstructionType',
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:promotion_coupon:generator_instruction_form']);
};
