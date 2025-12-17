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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Model\GatewayConfig;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigTest extends TestCase
{
    private GatewayConfig $gatewayConfig;

    protected function setUp(): void
    {
        $this->gatewayConfig = new GatewayConfig();
    }

    public function testItImplementsGatewayConfigInterface(): void
    {
        $this->assertInstanceOf(GatewayConfigInterface::class, $this->gatewayConfig);
    }

    public function testItsGatewayNameIsMutable(): void
    {
        $this->gatewayConfig->setGatewayName('Offline');
        $this->assertSame('Offline', $this->gatewayConfig->getGatewayName());
    }

    public function testItGetsFactoryNameFromConfigIfVariableNotSet(): void
    {
        $this->gatewayConfig->setConfig(['factory' => 'Offline']);
        $this->assertSame('Offline', $this->gatewayConfig->getFactoryName());
    }

    public function testItsFactoryNameIsMutable(): void
    {
        $this->gatewayConfig->setFactoryName('Offline');
        $this->assertSame('Offline', $this->gatewayConfig->getFactoryName());
    }

    public function testItsConfigIsMutable(): void
    {
        $this->gatewayConfig->setConfig(['key' => '123']);
        $this->assertSame(['key' => '123'], $this->gatewayConfig->getConfig());
    }
}
