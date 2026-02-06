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

use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ApplyCreateTransitionOnOrderListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ProcessCartListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderCheckoutStateListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderPaymentStateListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderShippingStateListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\SaveCheckoutCompletionDateListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.listener.workflow.order_checkout.process_cart', ProcessCartListener::class)
        ->args([service('sylius.order_processing.order_processor')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.address', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.select_shipping', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.skip_shipping', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.select_payment', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.skip_payment', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order_checkout.apply_create_transition_on_order', ApplyCreateTransitionOnOrderListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 400]);

    $services->set('sylius.listener.workflow.order_checkout.save_checkout_completion_date', SaveCheckoutCompletionDateListener::class)
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 300]);

    $services->set('sylius.listener.workflow.order_checkout.resolve_order_checkout_state', ResolveOrderCheckoutStateListener::class)
        ->args([service('sylius.state_resolver.checkout')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.address', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.select_shipping', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order_checkout.resolve_order_payment_state', ResolveOrderPaymentStateListener::class)
        ->args([service('sylius.state_resolver.order_payment')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order_checkout.resolve_order_shipping_state', ResolveOrderShippingStateListener::class)
        ->args([service('sylius.state_resolver.order_shipping')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 100]);
};
