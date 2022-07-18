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

namespace spec\Sylius\Component\Core\Factory;

use Payum\Core\Model\GatewayConfigInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PaymentMethodFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory, FactoryInterface $gatewayConfigFactory): void
    {
        $this->beConstructedWith($decoratedFactory, $gatewayConfigFactory);
    }

    function it_implements_payment_method_factory_interface(): void
    {
        $this->shouldImplement(PaymentMethodFactoryInterface::class);
    }

    function it_creates_payment_method_with_specific_gateway(
        FactoryInterface $decoratedFactory,
        FactoryInterface $gatewayConfigFactory,
        GatewayConfigInterface $gatewayConfig,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $gatewayConfigFactory->createNew()->willReturn($gatewayConfig);
        $gatewayConfig->setFactoryName('offline')->shouldBeCalled();

        $decoratedFactory->createNew()->willReturn($paymentMethod);
        $paymentMethod->setGatewayConfig($gatewayConfig)->shouldBeCalled();

        $this->createWithGateway('offline');
    }
}
