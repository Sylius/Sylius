<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Sylius\Component\Core\Model\Shipment;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $shipment = $framework->workflows()->workflows('sylius_shipment');
    $shipment
        ->type('state_machine')
        ->supports([Shipment::class])
        ->initialMarking(['cart']);

    $shipment->markingStore()
        ->type('method')
        ->property('state');

    $shipment->place()->name('cart');
    $shipment->place()->name('ready');
    $shipment->place()->name('shipped');
    $shipment->place()->name('cancelled');

    $shipment->transition()
        ->name('create')
        ->from(['cart'])
        ->to(['ready']);

    $shipment->transition()
        ->name('ship')
        ->from(['ready'])
        ->to(['shipped']);

    $shipment->transition()
        ->name('cancel')
        ->from(['ready'])
        ->to(['cancelled']);
};
