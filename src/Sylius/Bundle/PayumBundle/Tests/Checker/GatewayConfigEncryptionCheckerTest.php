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

namespace Sylius\Bundle\PayumBundle\Tests\Checker;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Checker\GatewayConfigEncryptionCheckerInterface as BaseGatewayConfigEncryptionCheckerInterface;
use Sylius\Bundle\PayumBundle\Checker\GatewayConfigEncryptionChecker;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;

final class GatewayConfigEncryptionCheckerTest extends TestCase
{
    /** @test */
    public function it_cannot_encrypt_when_base_class_returns_false(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $decoratedChecker = $this->createMock(BaseGatewayConfigEncryptionCheckerInterface::class);
        $decoratedChecker->method('isEncryptionEnabled')->willReturn(false);

        $checker = new GatewayConfigEncryptionChecker($decoratedChecker);
        $this->assertFalse($checker->isEncryptionEnabled($gatewayConfig));
    }

    /** @test */
    public function it_cannot_encrypt_when_gateway_config_uses_payum(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getUsePayum')->willReturn(true);
        $decoratedChecker = $this->createMock(BaseGatewayConfigEncryptionCheckerInterface::class);
        $decoratedChecker->method('isEncryptionEnabled')->willReturn(true);

        $checker = new GatewayConfigEncryptionChecker($decoratedChecker);
        $this->assertFalse($checker->isEncryptionEnabled($gatewayConfig));
    }

    /** @test */
    public function it_can_encrypt_when_base_class_returns_true_and_gateway_config_does_not_use_payum(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getUsePayum')->willReturn(false);
        $decoratedChecker = $this->createMock(BaseGatewayConfigEncryptionCheckerInterface::class);
        $decoratedChecker->method('isEncryptionEnabled')->willReturn(true);

        $checker = new GatewayConfigEncryptionChecker($decoratedChecker);
        $this->assertTrue($checker->isEncryptionEnabled($gatewayConfig));
    }
}
