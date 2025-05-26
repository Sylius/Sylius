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

namespace Tests\Sylius\Component\Payment\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentMethodTest extends TestCase
{
    private PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        $this->paymentMethod = new PaymentMethod();
        $this->paymentMethod->setCurrentLocale('en_US');
        $this->paymentMethod->setFallbackLocale('en_US');
    }

    public function testImplementsSyliusPaymentMethodInterface(): void
    {
        $this->assertInstanceOf(PaymentMethodInterface::class, $this->paymentMethod);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->paymentMethod->getId());
    }

    public function testItsCodeIsMutable(): void
    {
        $this->paymentMethod->setCode('PM1');
        $this->assertSame('PM1', $this->paymentMethod->getCode());
    }

    public function testUnnamedByDefault(): void
    {
        $this->assertNull($this->paymentMethod->getName());
    }

    public function testItsNameIsMutable(): void
    {
        $this->paymentMethod->setName('Stripe');
        $this->assertSame('Stripe', $this->paymentMethod->getName());
    }

    public function testConvertibleToStringAndReturnsItsName(): void
    {
        $this->paymentMethod->setName('PayPal');
        $this->assertSame('PayPal', $this->paymentMethod->__toString());
    }

    public function testHasNoDescriptionByDefault(): void
    {
        $this->assertNull($this->paymentMethod->getDescription());
    }

    public function testItsDescriptionIsMutable(): void
    {
        $this->paymentMethod->setDescription('Pay by check.');
        $this->assertSame('Pay by check.', $this->paymentMethod->getDescription());
    }

    public function testItsInstructionsIsMutable(): void
    {
        $this->paymentMethod->setInstructions('Pay on account: 1100012312');
        $this->assertSame('Pay on account: 1100012312', $this->paymentMethod->getInstructions());
    }

    public function testHasNoAppEnvironmentByDefault(): void
    {
        $this->assertNull($this->paymentMethod->getEnvironment());
    }

    public function testItsAppEnvironmentIsMutable(): void
    {
        $this->paymentMethod->setEnvironment('dev');
        $this->assertSame('dev', $this->paymentMethod->getEnvironment());
    }

    public function testHasNoPositionByDefault(): void
    {
        $this->assertNull($this->paymentMethod->getPosition());
    }

    public function testItsPositionIsMutable(): void
    {
        $this->paymentMethod->setPosition(10);
        $this->assertSame(10, $this->paymentMethod->getPosition());
    }

    public function testEnabledByDefault(): void
    {
        self::assertTrue($this->paymentMethod->isEnabled());
    }

    public function testAllowsDisablingItself(): void
    {
        $this->paymentMethod->setEnabled(false);
        self::assertFalse($this->paymentMethod->isEnabled());
    }

    public function testHasNoLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->paymentMethod->getUpdatedAt());
    }

    public function testItsGatewayConfigIsMutable(): void
    {
        /** @var GatewayConfigInterface&MockObject $gatewayConfigMock */
        $gatewayConfigMock = $this->createMock(GatewayConfigInterface::class);
        $this->paymentMethod->setGatewayConfig($gatewayConfigMock);
        $this->assertSame($gatewayConfigMock, $this->paymentMethod->getGatewayConfig());
    }
}
