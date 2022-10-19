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

use Sylius\Component\Core\Model\Payment;
use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $payment = $framework->workflows()->workflows('sylius_payment');
    $payment
        ->type('state_machine')
        ->supports([Payment::class])
        ->initialMarking(['cart']);

    $payment->markingStore()
        ->type('method')
        ->property('state');

    $payment->place()->name('cart');
    $payment->place()->name('new');
    $payment->place()->name('processing');
    $payment->place()->name('authorized');
    $payment->place()->name('completed');
    $payment->place()->name('failed');
    $payment->place()->name('cancelled');
    $payment->place()->name('refunded');

    $payment->transition()
        ->name('create')
        ->from(['cart'])
        ->to(['new']);

    $payment->transition()
        ->name('process')
        ->from(['new'])
        ->to(['processing']);

    $payment->transition()
        ->name('authorize')
        ->from(['new', 'processing'])
        ->to(['authorized']);

    $payment->transition()
        ->name('complete')
        ->from(['new', 'processing', 'authorized'])
        ->to(['completed']);

    $payment->transition()
        ->name('fail')
        ->from(['new', 'processing'])
        ->to(['failed']);

    $payment->transition()
        ->name('cancel')
        ->from(['new', 'processing', 'authorized'])
        ->to(['cancelled']);

    $payment->transition()
        ->name('refund')
        ->from(['completed'])
        ->to(['refunded']);
};
