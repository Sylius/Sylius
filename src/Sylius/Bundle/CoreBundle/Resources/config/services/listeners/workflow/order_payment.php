<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.workflow.order_payment.sell_order_inventory', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment\SellOrderInventoryListener')
        ->args([service('sylius.operator.inventory.order_inventory')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_payment.completed.pay', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order_payment.resolve_order_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderPayment\ResolveOrderStateListener')
        ->args([service('sylius.state_resolver.order')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_payment.completed.pay', 'priority' => 100]);
};
