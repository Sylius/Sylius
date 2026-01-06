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

namespace Tests\Sylius\Bundle\CoreBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OrderShippingMethodEligibility;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderShippingMethodEligibilityValidatorTest extends TestCase
{
    private MockObject&ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private ExecutionContextInterface&MockObject $context;

    private OrderShippingMethodEligibilityValidator $validator;

    protected function setUp(): void
    {
        $this->eligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->validator = new OrderShippingMethodEligibilityValidator($this->eligibilityChecker);
        $this->validator->initialize($this->context);
    }

    public function testItIsAConstraintValidator(): void
    {
        $this->assertInstanceOf(ConstraintValidatorInterface::class, $this->validator);
    }

    public function testItThrowsExceptionIfConstraintIsNotCorrectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->validator->validate('', $this->createMock(Constraint::class));
    }

    public function testItAddsViolationForNotAvailableShippingMethods(): void
    {
        $constraint = new OrderShippingMethodEligibility();

        $channel = $this->createMock(ChannelInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $shipmentOne = $this->createMock(ShipmentInterface::class);
        $shipmentTwo = $this->createMock(ShipmentInterface::class);
        $shippingMethodOne = $this->createMock(ShippingMethodInterface::class);
        $shippingMethodTwo = $this->createMock(ShippingMethodInterface::class);
        $channelsOne = $this->createMock(Collection::class);
        $channelsTwo = $this->createMock(Collection::class);

        $order->method('getShipments')->willReturn(new ArrayCollection([$shipmentOne, $shipmentTwo]));
        $order->method('getChannel')->willReturn($channel);

        $shipmentOne->method('getMethod')->willReturn($shippingMethodOne);
        $shipmentTwo->method('getMethod')->willReturn($shippingMethodTwo);

        $shippingMethodOne->method('isEnabled')->willReturn(false);
        $shippingMethodTwo->method('isEnabled')->willReturn(true);
        $shippingMethodOne->method('getChannels')->willReturn($channelsOne);
        $shippingMethodTwo->method('getChannels')->willReturn($channelsTwo);

        $shippingMethodOne->method('getName')->willReturn('Shipping method one');
        $shippingMethodTwo->method('getName')->willReturn('Shipping method two');

        $channelsOne->method('contains')->with($channel)->willReturn(true);
        $channelsTwo->method('contains')->with($channel)->willReturn(false);

        $this->context->expects($this->exactly(2))
            ->method('addViolation')
            ->willReturnCallback(static function (string $message, array $params) {
                static $calls = 0;
                ++$calls;
                if ($calls === 1) {
                    TestCase::assertSame('sylius.order.shipping_method_not_available', $message);
                    TestCase::assertSame(['%shippingMethodName%' => 'Shipping method one'], $params);
                } elseif ($calls === 2) {
                    TestCase::assertSame('sylius.order.shipping_method_not_available', $message);
                    TestCase::assertSame(['%shippingMethodName%' => 'Shipping method two'], $params);
                }
            })
        ;

        $this->validator->validate($order, $constraint);
    }

    public function testItAddsViolationIfEligibilityFails(): void
    {
        $constraint = new OrderShippingMethodEligibility();

        $channel = $this->createMock(ChannelInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $channels = $this->createMock(Collection::class);

        $order->method('getShipments')->willReturn(new ArrayCollection([$shipment]));
        $order->method('getChannel')->willReturn($channel);
        $shipment->method('getMethod')->willReturn($shippingMethod);
        $shippingMethod->method('isEnabled')->willReturn(true);
        $shippingMethod->method('getChannels')->willReturn($channels);
        $channels->method('contains')->with($channel)->willReturn(true);
        $shippingMethod->method('getName')->willReturn('InPost');
        $this->eligibilityChecker->method('isEligible')->willReturn(false);

        $this->context
            ->expects($this->once())
            ->method('addViolation')
            ->with('sylius.order.shipping_method_eligibility', ['%shippingMethodName%' => 'InPost'])
        ;

        $this->validator->validate($order, $constraint);
    }

    public function testItDoesNotAddViolationIfShippingMethodIsAvailableAndEligible(): void
    {
        $constraint = new OrderShippingMethodEligibility();

        $channel = $this->createMock(ChannelInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);
        $shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $channels = $this->createMock(Collection::class);

        $order->method('getShipments')->willReturn(new ArrayCollection([$shipment]));
        $order->method('getChannel')->willReturn($channel);
        $shipment->method('getMethod')->willReturn($shippingMethod);
        $shippingMethod->method('isEnabled')->willReturn(true);
        $shippingMethod->method('getChannels')->willReturn($channels);
        $channels->method('contains')->with($channel)->willReturn(true);
        $shippingMethod->method('getName')->willReturn('InPost');
        $this->eligibilityChecker->method('isEligible')->willReturn(true);

        $this->context->expects($this->never())->method('addViolation');

        $this->validator->validate($order, $constraint);
    }
}
