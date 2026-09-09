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

use Sylius\Bundle\ApiBundle\Applicator\ArchivingPromotionApplicator;
use Sylius\Bundle\ApiBundle\Applicator\ArchivingPromotionApplicatorInterface;
use Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicator;
use Sylius\Bundle\ApiBundle\Applicator\ArchivingShippingMethodApplicatorInterface;
use Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicator;
use Sylius\Bundle\ApiBundle\Applicator\OrderStateMachineTransitionApplicatorInterface;
use Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicator;
use Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicatorInterface;
use Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicator;
use Sylius\Bundle\ApiBundle\Applicator\ProductReviewStateMachineTransitionApplicatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_api.applicator.archiving_shipping_method', ArchivingShippingMethodApplicator::class)
        ->args([service('clock')])
    ;
    $services->alias(ArchivingShippingMethodApplicatorInterface::class, 'sylius_api.applicator.archiving_shipping_method');

    $services
        ->set('sylius_api.applicator.order_state_machine_transition', OrderStateMachineTransitionApplicator::class)
        ->args([service('sylius_abstraction.state_machine')])
    ;
    $services->alias(OrderStateMachineTransitionApplicatorInterface::class, 'sylius_api.applicator.order_state_machine_transition');

    $services
        ->set('sylius_api.applicator.payment_state_machine_transition', PaymentStateMachineTransitionApplicator::class)
        ->args([service('sylius_abstraction.state_machine')])
    ;
    $services->alias(PaymentStateMachineTransitionApplicatorInterface::class, 'sylius_api.applicator.payment_state_machine_transition');

    $services
        ->set('sylius_api.applicator.product_review_state_machine_transition', ProductReviewStateMachineTransitionApplicator::class)
        ->args([service('sylius_abstraction.state_machine')])
    ;
    $services->alias(ProductReviewStateMachineTransitionApplicatorInterface::class, 'sylius_api.applicator.product_review_state_machine_transition');

    $services
        ->set('sylius_api.applicator.archiving_promotion', ArchivingPromotionApplicator::class)
        ->args([service('clock')])
    ;
    $services->alias(ArchivingPromotionApplicatorInterface::class, 'sylius_api.applicator.archiving_promotion');
};
