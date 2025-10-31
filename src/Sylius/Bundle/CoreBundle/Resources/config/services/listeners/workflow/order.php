<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.listener.workflow.order.assign_order_number', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderNumberListener')
        ->args([service('sylius.number_assigner.order_number')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.transition.create', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order.assign_order_token', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderTokenListener')
        ->args([service('sylius.assigner.order_token.unique_id_based')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.transition.create', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order.create_payment', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreatePaymentListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 600]);

    $services->set('sylius.listener.workflow.order.create_shipment', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreateShipmentListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 500]);

    $services->set('sylius.listener.workflow.order.decrement_promotion_usages', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\DecrementPromotionUsagesListener')
        ->args([service('sylius.modifier.promotion.order_usage')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order.increment_promotion_usages', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\IncrementPromotionUsagesListener')
        ->args([service('sylius.modifier.promotion.order_usage')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 300]);

    $services->set('sylius.listener.workflow.order.hold_inventory', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\HoldInventoryListener')
        ->args([service('sylius.operator.inventory.order_inventory')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 400]);

    $services->set('sylius.listener.workflow.order.give_back_inventory', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\GiveBackInventoryListener')
        ->args([service('sylius.operator.inventory.order_inventory')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order.request_order_payment', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderPaymentListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 700]);

    $services->set('sylius.listener.workflow.order.request_order_shipping', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderShippingListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 800]);

    $services->set('sylius.listener.workflow.order.save_customer_addresses', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SaveCustomerAddressesListener')
        ->args([service('sylius.saver.customer.order_addresses')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order.set_immutable_names', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SetImmutableNamesListener')
        ->args([service('sylius.setter.order.item_names')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order.cancel_order_payment', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderPaymentListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 400]);

    $services->set('sylius.listener.workflow.order.cancel_order_shipping', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderShippingListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 300]);

    $services->set('sylius.listener.workflow.order.cancel_payment', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelPaymentListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 600]);

    $services->set('sylius.listener.workflow.order.cancel_shipment', 'Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelShipmentListener')
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 500]);
};
