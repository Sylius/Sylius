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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Factory\PaymentMethodFactory;
use Sylius\Component\Core\Factory\PaymentMethodFactoryInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class PaymentMethodFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private FactoryInterface&MockObject $gatewayConfigFactory;

    private PaymentMethodFactory $factory;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->gatewayConfigFactory = $this->createMock(FactoryInterface::class);
        $this->factory = new PaymentMethodFactory($this->decoratedFactory, $this->gatewayConfigFactory);
    }

    public function testShouldImplementPaymentMethodFactoryInterface(): void
    {
        $this->assertInstanceOf(PaymentMethodFactoryInterface::class, $this->factory);
    }

    public function testShouldCreatePaymentMethodWithSpecificGateway(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->gatewayConfigFactory->expects($this->once())->method('createNew')->willReturn($gatewayConfig);
        $gatewayConfig->expects($this->once())->method('setFactoryName')->with('offline');
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($paymentMethod);
        $paymentMethod->expects($this->once())->method('setGatewayConfig')->with($gatewayConfig);

        $this->factory->createWithGateway('offline');
    }
}
