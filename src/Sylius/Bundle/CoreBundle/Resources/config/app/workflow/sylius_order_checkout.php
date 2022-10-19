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
    $orderCheckout = $framework->workflows()->workflows('sylius_order_checkout');
    $orderCheckout
        ->type('state_machine')
        ->supports([Order::class])
        ->initialMarking(['cart']);

    $orderCheckout->markingStore()
        ->type('method')
        ->property('checkoutState');

    $orderCheckout->place()->name('cart');
    $orderCheckout->place()->name('addressed');
    $orderCheckout->place()->name('shipping_selected');
    $orderCheckout->place()->name('shipping_skipped');
    $orderCheckout->place()->name('payment_skipped');
    $orderCheckout->place()->name('payment_selected');
    $orderCheckout->place()->name('completed');

    $orderCheckout->transition()
        ->name('address')
        ->from(['cart', 'addressed', 'shipping_selected', 'shipping_skipped', 'payment_selected', 'payment_skipped'])
        ->to(['addressed']);

    $orderCheckout->transition()
        ->name('skip_shipping')
        ->from(['addressed'])
        ->to(['shipping_skipped']);

    $orderCheckout->transition()
        ->name('select_shipping')
        ->from(['addressed', 'shipping_selected', 'payment_selected', 'payment_skipped'])
        ->to(['shipping_selected']);

    $orderCheckout->transition()
        ->name('skip_payment')
        ->from(['shipping_selected', 'shipping_skipped'])
        ->to(['payment_skipped']);

    $orderCheckout->transition()
        ->name('select_payment')
        ->from(['payment_selected', 'shipping_skipped', 'shipping_selected'])
        ->to(['payment_selected']);

    $orderCheckout->transition()
        ->name('complete')
        ->from(['payment_selected', 'payment_skipped'])
        ->to(['completed']);
};
