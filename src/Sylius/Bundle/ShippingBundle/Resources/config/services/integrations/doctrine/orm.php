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

use Sylius\Bundle\ShippingBundle\Doctrine\ORM\Listener\UnusedShipmentUnitMappingListener;
use Sylius\Bundle\ShippingBundle\Doctrine\ORM\ShippingMethodRepository;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set('sylius.repository.shipping_method.class', ShippingMethodRepository::class);

    $services = $container->services();

    $services
        ->set('sylius.listener.unused_shipment_unit_mapping', UnusedShipmentUnitMappingListener::class)
        ->args(['%sylius.model.shipment_unit.class%'])
        ->tag('doctrine.event_listener', ['event' => 'loadClassMetadata', 'method' => 'loadClassMetadata', 'lazy' => true])
    ;
};
