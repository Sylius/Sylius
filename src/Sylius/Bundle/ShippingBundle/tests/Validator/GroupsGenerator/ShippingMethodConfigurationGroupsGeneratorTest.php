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

namespace Tests\Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShippingBundle\Validator\GroupsGenerator\ShippingMethodConfigurationGroupsGenerator;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Form\FormInterface;

final class ShippingMethodConfigurationGroupsGeneratorTest extends TestCase
{
    private MockObject&ShippingMethodInterface $shippingMethod;

    private ShippingMethodConfigurationGroupsGenerator $shippingMethodConfigurationGroupsGenerator;

    protected function setUp(): void
    {
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);

        $this->shippingMethodConfigurationGroupsGenerator = new ShippingMethodConfigurationGroupsGenerator([
            'flat_rate' => ['rate', 'sylius'],
            'per_unit_rate' => ['rate', 'sylius'],
        ]);
    }

    public function testThrowsErrorIfInvalidObjectIsPassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->shippingMethodConfigurationGroupsGenerator->__invoke(new \stdClass());
    }

    public function testReturnsShippingMethodConfigurationValidationGroups(): void
    {
        $this->shippingMethod->expects($this->once())->method('getCalculator')->willReturn('flat_rate');

        $this->assertSame(['rate', 'sylius'], $this->shippingMethodConfigurationGroupsGenerator->__invoke(($this->shippingMethod)));
    }

    public function testReturnsDefaultValidationGroups(): void
    {
        $this->shippingMethod->expects($this->once())->method('getCalculator')->willReturn(null);

        $this->assertSame(['sylius'], $this->shippingMethodConfigurationGroupsGenerator->__invoke(($this->shippingMethod)));
    }

    public function testReturnsGatewayConfigValidationGroupsIfItIsShippingMethodForm(): void
    {
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        $form->expects($this->once())->method('getData')->willReturn($this->shippingMethod);

        $this->shippingMethod->expects($this->once())->method('getCalculator')->willReturn('per_unit_rate');

        $this->assertSame(['rate', 'sylius'], $this->shippingMethodConfigurationGroupsGenerator->__invoke(($form)));
    }
}
