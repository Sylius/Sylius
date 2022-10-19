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

use Sylius\Component\Core\Model\Order;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $orderShipping = $framework->workflows()->workflows('sylius_order_shipping');
    $orderShipping
        ->type('state_machine')
        ->supports([Order::class])
        ->initialMarking(['cart']);

    $orderShipping->markingStore()
        ->type('method')
        ->property('shippingState');

    $orderShipping->place()->name('cart');
    $orderShipping->place()->name('ready');
    $orderShipping->place()->name('cancelled');
    $orderShipping->place()->name('partially_shipped');
    $orderShipping->place()->name('shipped');

    $orderShipping->transition()
        ->name('request_shipping')
        ->from(['cart'])
        ->to(['ready']);

    $orderShipping->transition()
        ->name('cancel')
        ->from(['ready'])
        ->to(['cancelled']);

    $orderShipping->transition()
        ->name('partially_ship')
        ->from(['ready'])
        ->to(['partially_shipped']);

    $orderShipping->transition()
        ->name('ship')
        ->from(['ready', 'partially_shipped'])
        ->to(['shipped']);
};
