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

namespace Tests\Sylius\Bundle\PaymentBundle\Generator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Generator\GatewayNameGenerator;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class GatewayNameGeneratorTest extends TestCase
{
    private GatewayNameGenerator $gatewayNameGenerator;

    private MockObject&PaymentMethodInterface $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gatewayNameGenerator = new GatewayNameGenerator();
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
    }

    public function testGeneratesGatewayConfigNameBasedOnPaymentMethodCode(): void
    {
        $this->paymentMethod->expects(self::once())
            ->method('getCode')
            ->willReturn('PayPal Express Checkout');

        self::assertSame('paypal_express_checkout', $this->gatewayNameGenerator->generate($this->paymentMethod));
    }

    public function testReturnsNullIfPaymentMethodCodeIsNull(): void
    {
        $this->paymentMethod->expects(self::once())->method('getCode')->willReturn(null);

        self::assertNull($this->gatewayNameGenerator->generate($this->paymentMethod));
    }
}
