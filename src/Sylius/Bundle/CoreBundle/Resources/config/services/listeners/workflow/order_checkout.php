<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.workflow.order_checkout.process_cart', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ProcessCartListener')
        ->args([service('sylius.order_processing.order_processor')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.address', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.select_shipping', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.skip_shipping', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.select_payment', 'priority' => 200])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.skip_payment', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order_checkout.apply_create_transition_on_order', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ApplyCreateTransitionOnOrderListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 400]);

    $services->set('sylius.listener.workflow.order_checkout.save_checkout_completion_date', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\SaveCheckoutCompletionDateListener')
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 300]);

    $services->set('sylius.listener.workflow.order_checkout.resolve_order_checkout_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderCheckoutStateListener')
        ->args([service('sylius.state_resolver.checkout')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.address', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.select_shipping', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order_checkout.resolve_order_payment_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderPaymentStateListener')
        ->args([service('sylius.state_resolver.order_payment')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order_checkout.resolve_order_shipping_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ResolveOrderShippingStateListener')
        ->args([service('sylius.state_resolver.order_shipping')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 100]);
};
