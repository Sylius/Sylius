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

use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderNumberListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\AssignOrderTokenListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderPaymentListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderShippingListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelPaymentListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelShipmentListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreatePaymentListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreateShipmentListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\DecrementPromotionUsagesListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\GiveBackInventoryListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\HoldInventoryListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\IncrementPromotionUsagesListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderPaymentListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderShippingListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SaveCustomerAddressesListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\SetImmutableNamesListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.listener.workflow.order.assign_order_number', AssignOrderNumberListener::class)
        ->args([service('sylius.number_assigner.order_number')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.transition.create', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order.assign_order_token', AssignOrderTokenListener::class)
        ->args([service('sylius.assigner.order_token.unique_id_based')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.transition.create', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order.create_payment', CreatePaymentListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 600]);

    $services->set('sylius.listener.workflow.order.create_shipment', CreateShipmentListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 500]);

    $services->set('sylius.listener.workflow.order.decrement_promotion_usages', DecrementPromotionUsagesListener::class)
        ->args([service('sylius.modifier.promotion.order_usage')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order.increment_promotion_usages', IncrementPromotionUsagesListener::class)
        ->args([service('sylius.modifier.promotion.order_usage')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 300]);

    $services->set('sylius.listener.workflow.order.hold_inventory', HoldInventoryListener::class)
        ->args([service('sylius.operator.inventory.order_inventory')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 400]);

    $services->set('sylius.listener.workflow.order.give_back_inventory', GiveBackInventoryListener::class)
        ->args([service('sylius.operator.inventory.order_inventory')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order.request_order_payment', RequestOrderPaymentListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 700]);

    $services->set('sylius.listener.workflow.order.request_order_shipping', RequestOrderShippingListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 800]);

    $services->set('sylius.listener.workflow.order.save_customer_addresses', SaveCustomerAddressesListener::class)
        ->args([service('sylius.saver.customer.order_addresses')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 200]);

    $services->set('sylius.listener.workflow.order.set_immutable_names', SetImmutableNamesListener::class)
        ->args([service('sylius.setter.order.item_names')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.create', 'priority' => 100]);

    $services->set('sylius.listener.workflow.order.cancel_order_payment', CancelOrderPaymentListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 400]);

    $services->set('sylius.listener.workflow.order.cancel_order_shipping', CancelOrderShippingListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 300]);

    $services->set('sylius.listener.workflow.order.cancel_payment', CancelPaymentListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 600]);

    $services->set('sylius.listener.workflow.order.cancel_shipment', CancelShipmentListener::class)
        ->args([service('sylius_abstraction.state_machine')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order.completed.cancel', 'priority' => 500]);
};
