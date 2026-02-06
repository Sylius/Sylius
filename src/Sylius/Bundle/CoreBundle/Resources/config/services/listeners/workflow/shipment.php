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

use Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\AssignShippingDateListener;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment\ResolveOrderShipmentStateListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.listener.workflow.shipment.resolve_order_shipment_state', ResolveOrderShipmentStateListener::class)
        ->args([service('sylius.state_resolver.order_shipping')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_shipment.completed.ship', 'priority' => 100]);

    $services->set('sylius.listener.workflow.shipment.assign_shipping_date', AssignShippingDateListener::class)
        ->args([service('sylius.assigner.shipping_date')])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_shipment.transition.ship', 'priority' => 100]);
};
