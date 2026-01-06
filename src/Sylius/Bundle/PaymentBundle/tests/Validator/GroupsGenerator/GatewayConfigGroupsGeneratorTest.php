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

namespace Tests\Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGenerator;
use Sylius\Component\Payment\Model\GatewayConfigInterface;

final class GatewayConfigGroupsGeneratorTest extends TestCase
{
    private GatewayConfigGroupsGenerator $gatewayConfigGroupsGenerator;

    private GatewayConfigInterface&MockObject $gatewayConfig;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gatewayConfigGroupsGenerator = new GatewayConfigGroupsGenerator(['sylius'], [
            'paypal_express_checkout' => ['paypal_express_checkout', 'sylius'],
            'stripe_checkout' => ['stripe_checkout', 'sylius'],
        ]);
        $this->gatewayConfig = $this->createMock(GatewayConfigInterface::class);
    }

    public function testReturnsGatewayConfigValidationGroups(): void
    {
        $this->gatewayConfig->method('getFactoryName')->willReturn('paypal_express_checkout');

        $result = ($this->gatewayConfigGroupsGenerator)($this->gatewayConfig);

        self::assertEquals(['paypal_express_checkout', 'sylius'], $result);
    }

    public function testReturnsDefaultValidationGroupsIfFactoryNameIsNull(): void
    {
        $this->gatewayConfig->method('getFactoryName')->willReturn(null);

        $result = ($this->gatewayConfigGroupsGenerator)($this->gatewayConfig);

        self::assertEquals(['sylius'], $result);
    }
}
