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
use Sylius\Bundle\PayumBundle\Checker\PayumGatewayConfigEncryptionChecker;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface as BaseGatewayConfigInterface;

final class PayumGatewayConfigEncryptionCheckerTest extends TestCase
{
    /** @test */
    public function it_cannot_detect_encrypt_if_gateway_config_does_not_implement_crypted_interface(): void
    {
        $gatewayConfig = $this->createMock(BaseGatewayConfigInterface::class);

        $checker = new PayumGatewayConfigEncryptionChecker();
        $this->assertFalse($checker->isPayumEncryptionEnabled($gatewayConfig));
    }

    /** @test */
    public function it_cannot_encrypt_if_gateway_config_does_not_use_payum(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getUsePayum')->willReturn(false);

        $checker = new PayumGatewayConfigEncryptionChecker();
        $this->assertFalse($checker->isPayumEncryptionEnabled($gatewayConfig));
    }

    /** @test */
    public function it_can_encrypt_if_gateway_config_uses_payum(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getUsePayum')->willReturn(true);

        $checker = new PayumGatewayConfigEncryptionChecker();
        $this->assertTrue($checker->isPayumEncryptionEnabled($gatewayConfig));
    }
}
