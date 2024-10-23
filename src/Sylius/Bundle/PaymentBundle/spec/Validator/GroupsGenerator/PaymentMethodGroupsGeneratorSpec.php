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

namespace spec\Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGeneratorInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentMethodGroupsGeneratorSpec extends ObjectBehavior
{
    function let(GatewayConfigGroupsGeneratorInterface $gatewayConfigGroupsGenerator): void
    {
        $this->beConstructedWith(['sylius'], $gatewayConfigGroupsGenerator);
    }

    function it_returns_payment_method_validation_groups(
        GatewayConfigGroupsGeneratorInterface $gatewayConfigGroupsGenerator,
        GatewayConfigInterface $gatewayConfig,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $paymentMethod->getGatewayConfig()->willReturn($gatewayConfig);
        $gatewayConfigGroupsGenerator->__invoke($gatewayConfig)->willReturn(['paypal_express_checkout', 'sylius']);

        $this($paymentMethod)->shouldReturn(['sylius', 'paypal_express_checkout']);
    }

    function it_returns_default_validation_groups_if_gateway_config_is_null(
        PaymentMethodInterface $paymentMethod,
    ): void {
        $paymentMethod->getGatewayConfig()->willReturn(null);

        $this($paymentMethod)->shouldReturn(['sylius']);
    }
}
