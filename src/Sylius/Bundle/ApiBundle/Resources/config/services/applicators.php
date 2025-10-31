<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.applicator.archiving_shipping_method', 'Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicator')
        ->args([service('clock')]);

    $services->alias('Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicatorInterface', 'sylius_api.applicator.archiving_shipping_method');

    $services->set('sylius_api.applicator.order_state_machine_transition', 'Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicator')
        ->args([service('sylius_abstraction.state_machine')]);

    $services->alias('Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicatorInterface', 'sylius_api.applicator.order_state_machine_transition');

    $services->set('sylius_api.applicator.payment_state_machine_transition', 'Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicator')
        ->args([service('sylius_abstraction.state_machine')]);

    $services->alias('Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicatorInterface', 'sylius_api.applicator.payment_state_machine_transition');

    $services->set('sylius_api.applicator.product_review_state_machine_transition', 'Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicator')
        ->args([service('sylius_abstraction.state_machine')]);

    $services->alias('Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicatorInterface', 'sylius_api.applicator.product_review_state_machine_transition');

    $services->set('sylius_api.applicator.archiving_promotion', 'Sylius\Bundle\ApiBundle\Applicator\ArchivingPromotionApplicator')
        ->args([service('clock')]);

    $services->alias('Sylius\Bundle\ApiBundle\Applicator\ArchivingPromotionApplicatorInterface', 'sylius_api.applicator.archiving_promotion');
};
