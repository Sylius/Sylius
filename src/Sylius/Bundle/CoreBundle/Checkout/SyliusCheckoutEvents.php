<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Checkout;

/**
 * Sylius checkout events.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class SyliusCheckoutEvents
{
    const ADDRESSING_INITIALIZE   = 'sylius.checkout.addressing.initialize';
    const ADDRESSING_PRE_COMPLETE = 'sylius.checkout.addressing.pre_complete';
    const ADDRESSING_COMPLETE     = 'sylius.checkout.addressing.complete';

    const SHIPPING_INITIALIZE   = 'sylius.checkout.shipping.initialize';
    const SHIPPING_PRE_COMPLETE = 'sylius.checkout.shipping.pre_complete';
    const SHIPPING_COMPLETE     = 'sylius.checkout.shipping.pre_complete';

    const PAYMENT_INITIALIZE   = 'sylius.checkout.payment.initialize';
    const PAYMENT_PRE_COMPLETE = 'sylius.checkout.payment.pre_complete';
    const PAYMENT_COMPLETE     = 'sylius.checkout.payment.pre_complete';

    const FINALIZE_INITIALIZE   = 'sylius.checkout.finalize.initialize';
    const FINALIZE_PRE_COMPLETE = 'sylius.checkout.finalize.pre_complete';
    const FINALIZE_COMPLETE     = 'sylius.checkout.finalize.pre_complete';
}
