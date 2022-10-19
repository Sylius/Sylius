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
    $orderPayment = $framework->workflows()->workflows('sylius_order_payment');
    $orderPayment
        ->type('state_machine')
        ->supports([Order::class])
        ->initialMarking(['cart']);

    $orderPayment->markingStore()
        ->type('method')
        ->property('paymentState');

    $orderPayment->place()->name('cart');
    $orderPayment->place()->name('awaiting_payment');
    $orderPayment->place()->name('partially_authorized');
    $orderPayment->place()->name('authorized');
    $orderPayment->place()->name('partially_paid');
    $orderPayment->place()->name('cancelled');
    $orderPayment->place()->name('paid');
    $orderPayment->place()->name('partially_refunded');
    $orderPayment->place()->name('refunded');

    $orderPayment->transition()
        ->name('request_payment')
        ->from(['cart'])
        ->to(['awaiting_payment']);

    $orderPayment->transition()
        ->name('partially_authorize')
        ->from(['awaiting_payment', 'partially_authorized'])
        ->to(['partially_authorized']);

    $orderPayment->transition()
        ->name('authorize')
        ->from(['awaiting_payment', 'partially_authorized'])
        ->to(['authorized']);

    $orderPayment->transition()
        ->name('partially_pay')
        ->from(['awaiting_payment', 'partially_paid', 'partially_authorized'])
        ->to(['partially_paid']);

    $orderPayment->transition()
        ->name('cancel')
        ->from(['awaiting_payment', 'authorized', 'partially_authorized'])
        ->to(['cancelled']);

    $orderPayment->transition()
        ->name('pay')
        ->from(['awaiting_payment', 'partially_paid', 'authorized'])
        ->to(['paid']);

    $orderPayment->transition()
        ->name('partially_refund')
        ->from(['paid', 'partially_paid', 'partially_refunded'])
        ->to(['partially_refunded']);

    $orderPayment->transition()
        ->name('refund')
        ->from(['paid', 'partially_paid', 'partially_refunded'])
        ->to(['refunded']);
};
