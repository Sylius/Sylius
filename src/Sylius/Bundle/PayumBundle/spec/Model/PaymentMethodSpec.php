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

namespace spec\Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentMethod as BasePaymentMethod;

final class PaymentMethodSpec extends ObjectBehavior
{
    function it_is_a_payment_method(): void
    {
        $this->shouldHaveType(BasePaymentMethod::class);
    }

    function it_implements_payment_method_interface(): void
    {
        $this->shouldImplement(PaymentMethodInterface::class);
    }

    function its_gateway_config_is_mutable(GatewayConfigInterface $gatewayConfig): void
    {
        $this->setGatewayConfig($gatewayConfig);
        $this->getGatewayConfig()->shouldReturn($gatewayConfig);
    }
}
