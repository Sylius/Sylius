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

namespace spec\Sylius\Bundle\PaymentBundle\Generator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class GatewayConfigNameGeneratorSpec extends ObjectBehavior
{
    function it_generates_gateway_config_name_based_on_payment_method_code(
        PaymentMethodInterface $paymentMethod,
    ): void {
        $paymentMethod->getCode()->willReturn('PayPal Express Checkout');

        $this->generate($paymentMethod)->shouldReturn('paypal_express_checkout');
    }

    function it_returns_null_if_payment_method_code_is_null(
        PaymentMethodInterface $paymentMethod,
    ): void {
        $paymentMethod->getCode()->willReturn(null);

        $this->generate($paymentMethod)->shouldReturn(null);
    }
}
