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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibility;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderShippingMethodEligibilityValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&ShippingMethodEligibilityCheckerInterface $eligibilityChecker;

    private OrderShippingMethodEligibilityValidator $orderShippingMethodEligibilityValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->eligibilityChecker = $this->createMock(ShippingMethodEligibilityCheckerInterface::class);
        $this->orderShippingMethodEligibilityValidator = new OrderShippingMethodEligibilityValidator($this->orderRepository, $this->eligibilityChecker);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->orderShippingMethodEligibilityValidator);
    }

    public function testThrowsAnExceptionIfValueIsNotInstanceOfCompleteOrder(): void
    {
        self::expectException(\InvalidArgumentException::class);
        $this->orderShippingMethodEligibilityValidator->validate('', new OrderShippingMethodEligibility());
    }

    public function testThrowsAnExceptionIfConstraintIsNotInstanceOfOrderShippingMethodEligibility(): void
    {
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        self::expectException(\InvalidArgumentException::class);
        $this->orderShippingMethodEligibilityValidator->validate(new CompleteOrder('token'), $constraintMock);
    }

    public function testThrowsAnExceptionIfOrderIsNull(): void
    {
        $constraint = new OrderShippingMethodEligibility();
        $value = new CompleteOrder(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->orderShippingMethodEligibilityValidator->validate($value, $constraint);
    }

    public function testAddsAViolationForEveryNotAvailableShippingMethodAttachedToTheOrder(): void
    {
        /** @var ExecutionContextInterface|MockObject $contextMock */
        $contextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentOneMock */
        $shipmentOneMock = $this->createMock(ShipmentInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentTwoMock */
        $shipmentTwoMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodOneMock */
        $shippingMethodOneMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodTwoMock */
        $shippingMethodTwoMock = $this->createMock(ShippingMethodInterface::class);
        /** @var Collection|MockObject $channelsCollectionTwoMock */
        $channelsCollectionTwoMock = $this->createMock(Collection::class);

        $this->orderShippingMethodEligibilityValidator->initialize($contextMock);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKENVALUE'])
            ->willReturn($orderMock);

        $orderMock
            ->expects(self::once())
            ->method('getChannel')
            ->willReturn($channelMock);

        $orderMock
            ->expects(self::once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([$shipmentOneMock, $shipmentTwoMock]));

        $shipmentOneMock
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn($shippingMethodOneMock);

        $shipmentTwoMock
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn($shippingMethodTwoMock);

        $shippingMethodOneMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(false);

        $shippingMethodTwoMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $shippingMethodTwoMock
            ->expects(self::once())
            ->method('getChannels')
            ->willReturn($channelsCollectionTwoMock);

        $channelsCollectionTwoMock
            ->expects(self::once())
            ->method('contains')
            ->with($channelMock)
            ->willReturn(false);

        $shippingMethodOneMock
            ->expects(self::once())
            ->method('getName')
            ->willReturn('Shipping method one');

        $shippingMethodTwoMock
            ->expects(self::once())
            ->method('getName')
            ->willReturn('Shipping method two');

        $contextMock
            ->expects($this->exactly(2))
            ->method('addViolation')
            ->willReturnCallback(function (string $message, array $params) {
                static $calls = 0;
                ++$calls;

                if ($calls === 1) {
                    $this->assertSame('sylius.order.shipping_method_not_available', $message);
                    $this->assertSame(['%shippingMethodName%' => 'Shipping method one'], $params);
                } elseif ($calls === 2) {
                    $this->assertSame('sylius.order.shipping_method_not_available', $message);
                    $this->assertSame(['%shippingMethodName%' => 'Shipping method two'], $params);
                }
            });

        $this->orderShippingMethodEligibilityValidator->validate(
            new CompleteOrder('ORDERTOKENVALUE'),
            new OrderShippingMethodEligibility(),
        );
    }

    public function testDoesNotAddViolationIfAllShippingMethodsAreAvailable(): void
    {
        /** @var ExecutionContextInterface|MockObject $contextMock */
        $contextMock = $this->createMock(ExecutionContextInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentOneMock */
        $shipmentOneMock = $this->createMock(ShipmentInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentTwoMock */
        $shipmentTwoMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodOneMock */
        $shippingMethodOneMock = $this->createMock(ShippingMethodInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodTwoMock */
        $shippingMethodTwoMock = $this->createMock(ShippingMethodInterface::class);
        /** @var Collection|MockObject $channelsCollectionOneMock */
        $channelsCollectionOneMock = $this->createMock(Collection::class);
        /** @var Collection|MockObject $channelsCollectionTwoMock */
        $channelsCollectionTwoMock = $this->createMock(Collection::class);

        $this->orderShippingMethodEligibilityValidator->initialize($contextMock);

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKENVALUE'])
            ->willReturn($orderMock);

        $orderMock
            ->expects($this->exactly(2))
            ->method('getChannel')
            ->willReturn($channelMock);

        $orderMock
            ->expects(self::once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([$shipmentOneMock, $shipmentTwoMock]));

        $shipmentOneMock
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn($shippingMethodOneMock);

        $shipmentTwoMock
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn($shippingMethodTwoMock);

        $shippingMethodOneMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $shippingMethodTwoMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $shippingMethodOneMock
            ->expects(self::once())
            ->method('getChannels')
            ->willReturn($channelsCollectionOneMock);

        $shippingMethodTwoMock
            ->expects(self::once())
            ->method('getChannels')
            ->willReturn($channelsCollectionTwoMock);

        $channelsCollectionOneMock
            ->expects(self::once())
            ->method('contains')
            ->with($channelMock)
            ->willReturn(true);

        $channelsCollectionTwoMock
            ->expects(self::once())
            ->method('contains')
            ->with($channelMock)
            ->willReturn(true);

        $this->eligibilityChecker
            ->expects($this->exactly(2))
            ->method('isEligible')
            ->willReturn(true);

        $contextMock->expects(self::never())->method('addViolation');

        $this->orderShippingMethodEligibilityValidator->validate(
            new CompleteOrder('ORDERTOKENVALUE'),
            new OrderShippingMethodEligibility(),
        );
    }

    public function testAddsViolationIfShipmentDoesNotMatchWithShippingMethod(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var Collection|MockObject $channelsCollectionMock */
        $channelsCollectionMock = $this->createMock(Collection::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);
        $this->orderShippingMethodEligibilityValidator->initialize($executionContextMock);
        $constraint = new OrderShippingMethodEligibility();
        $value = new CompleteOrder(orderTokenValue: 'token');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'token'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getShipments')->willReturn(new ArrayCollection([$shipmentMock]));
        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $shipmentMock->expects(self::once())->method('getMethod')->willReturn($shippingMethodMock);
        $this->eligibilityChecker->expects(self::once())->method('isEligible')->with($shipmentMock, $shippingMethodMock)->willReturn(false);
        $shippingMethodMock->expects(self::once())->method('getName')->willReturn('InPost');
        $shippingMethodMock->expects(self::once())->method('isEnabled')->willReturn(true);
        $shippingMethodMock->expects(self::once())->method('getChannels')->willReturn($channelsCollectionMock);
        $channelsCollectionMock->expects(self::once())->method('contains')->with($channelMock)->willReturn(true);
        $executionContextMock->expects(self::once())->method('addViolation')->with('sylius.order.shipping_method_eligibility', ['%shippingMethodName%' => 'InPost'])
        ;
        $this->orderShippingMethodEligibilityValidator->validate($value, $constraint);
    }

    public function testDoesNotAddAViolationIfShipmentMatchesWithShippingMethod(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ShipmentInterface|MockObject $shipmentMock */
        $shipmentMock = $this->createMock(ShipmentInterface::class);
        /** @var ShippingMethodInterface|MockObject $shippingMethodMock */
        $shippingMethodMock = $this->createMock(ShippingMethodInterface::class);
        /** @var Collection|MockObject $channelsCollectionMock */
        $channelsCollectionMock = $this->createMock(Collection::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var ExecutionContextInterface|MockObject $executionContextMock */
        $executionContextMock = $this->createMock(ExecutionContextInterface::class);

        $this->orderShippingMethodEligibilityValidator->initialize($executionContextMock);

        $constraint = new OrderShippingMethodEligibility();
        $value = new CompleteOrder(orderTokenValue: 'token');

        $this->orderRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'token'])
            ->willReturn($orderMock);

        $orderMock
            ->expects(self::once())
            ->method('getShipments')
            ->willReturn(new ArrayCollection([$shipmentMock]));

        $orderMock
            ->expects(self::once())
            ->method('getChannel')
            ->willReturn($channelMock);

        $shipmentMock
            ->expects(self::once())
            ->method('getMethod')
            ->willReturn($shippingMethodMock);

        $shippingMethodMock
            ->expects(self::once())
            ->method('isEnabled')
            ->willReturn(true);

        $shippingMethodMock
            ->expects(self::once())
            ->method('getChannels')
            ->willReturn($channelsCollectionMock);

        $channelsCollectionMock
            ->expects(self::once())
            ->method('contains')
            ->with($channelMock)
            ->willReturn(true);

        $this->eligibilityChecker
            ->expects(self::once())
            ->method('isEligible')
            ->with($shipmentMock, $shippingMethodMock)
            ->willReturn(true);

        $executionContextMock
            ->expects(self::never())
            ->method('addViolation');

        $this->orderShippingMethodEligibilityValidator->validate($value, $constraint);
    }
}
