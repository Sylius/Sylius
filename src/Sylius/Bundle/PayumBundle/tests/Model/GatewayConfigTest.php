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

namespace Tests\Sylius\Bundle\PayumBundle\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Model\GatewayConfig;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;

final class GatewayConfigTest extends TestCase
{
    private GatewayConfig $gatewayConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gatewayConfig = new GatewayConfig();
    }

    public function testImplementsPayumGatewayConfigInterface(): void
    {
        self::assertInstanceOf(GatewayConfigInterface::class, $this->gatewayConfig);
    }
}
