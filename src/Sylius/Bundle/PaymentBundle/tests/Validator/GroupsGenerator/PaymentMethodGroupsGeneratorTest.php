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
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\GatewayConfigGroupsGeneratorInterface;
use Sylius\Bundle\PaymentBundle\Validator\GroupsGenerator\PaymentMethodGroupsGenerator;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentMethodGroupsGeneratorTest extends TestCase
{
    private GatewayConfigGroupsGeneratorInterface&MockObject $gatewayConfigGroupsGenerator;

    private PaymentMethodGroupsGenerator $paymentMethodGroupsGenerator;

    private MockObject&PaymentMethodInterface $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gatewayConfigGroupsGenerator = $this->createMock(GatewayConfigGroupsGeneratorInterface::class);
        $this->paymentMethodGroupsGenerator = new PaymentMethodGroupsGenerator(
            ['sylius'],
            $this->gatewayConfigGroupsGenerator,
        );
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
    }

    public function testReturnsPaymentMethodValidationGroups(): void
    {
        $gatewayConfig = $this->createMock(GatewayConfigInterface::class);

        $this->paymentMethod
            ->method('getGatewayConfig')
            ->willReturn($gatewayConfig);

        $this->gatewayConfigGroupsGenerator
            ->method('__invoke')
            ->with($gatewayConfig)
            ->willReturn(['paypal_express_checkout', 'sylius']);

        $result = ($this->paymentMethodGroupsGenerator)($this->paymentMethod);

        self::assertEquals(['sylius', 'paypal_express_checkout'], $result);
    }

    public function testReturnsDefaultValidationGroupsIfGatewayConfigIsNull(): void
    {
        $this->paymentMethod
            ->method('getGatewayConfig')
            ->willReturn(null);

        $result = ($this->paymentMethodGroupsGenerator)($this->paymentMethod);

        self::assertEquals(['sylius'], $result);
    }
}
