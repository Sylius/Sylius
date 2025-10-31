<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.workflow.shipment.resolve_order_shipment_state', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\ResolveOrderShipmentStateListener')
        ->args([service('sylius.state_resolver.order_shipping')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_shipment.completed.ship', 'priority' => 100]);

    $services->set('sylius.listener.workflow.shipment.assign_shipping_date', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\AssignShippingDateListener')
        ->args([service('sylius.assigner.shipping_date')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_shipment.transition.ship', 'priority' => 100]);
};
