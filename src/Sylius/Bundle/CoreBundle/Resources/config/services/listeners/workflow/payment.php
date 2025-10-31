<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.workflow.payment.process_order', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ProcessOrderListener')
        ->args([service('sylius.order_processing.order_payment_processor.after_checkout')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.fail', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.cancel', 'priority' => 100]);

    $services->set('sylius.listener.workflow.payment.resolve_order_payment_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Payment\ResolveOrderPaymentStateListener')
        ->args([service('sylius.state_resolver.order_payment')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.complete', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.process', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.refund', 'priority' => 100])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_payment.completed.authorize', 'priority' => 100]);
};
