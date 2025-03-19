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

namespace Sylius\Bundle\PaymentBundle\Tests\Checker;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Checker\GatewayConfigEncryptionChecker;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigEncryptionCheckerTest extends TestCase
{
    /** @test */
    public function it_cannot_encrypt_if_factory_name_is_in_disabled_factories(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getFactoryName')->willReturn('offline');

        $checker = new GatewayConfigEncryptionChecker(['offline']);
        $this->assertFalse($checker->isEncryptionEnabled($gatewayConfig));
    }

    /** @test */
    public function it_can_encrypt_if_factory_name_is_not_in_disabled_factories(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);
        $gatewayConfig->method('getFactoryName')->willReturn('sylius');

        $checker = new GatewayConfigEncryptionChecker(['offline']);
        $this->assertTrue($checker->isEncryptionEnabled($gatewayConfig));
    }
}
