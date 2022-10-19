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
    $order = $framework->workflows()->workflows('sylius_order');
    $order
        ->type('state_machine')
        ->supports([Order::class])
        ->initialMarking(['cart']);

    $order->markingStore()
        ->type('method')
        ->property('state');

    $order->place()->name('cart');
    $order->place()->name('new');
    $order->place()->name('cancelled');
    $order->place()->name('fulfilled');

    $order->transition()
        ->name('create')
        ->from(['cart'])
        ->to(['new']);

    $order->transition()
        ->name('cancel')
        ->from(['new'])
        ->to(['cancelled']);

    $order->transition()
        ->name('fulfill')
        ->from(['new'])
        ->to(['fulfilled']);
};
