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

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibility;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderShippingMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
    ): void {
        $this->beConstructedWith($orderRepository, $eligibilityChecker);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_constraint_does_not_extend_order_token_value_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {
            }])
        ;
    }

    function it_throws_an_exception_if_constraint_does_not_type_of_order_shipping_method_eligibility(): void
    {
        $constraint = new class() extends Constraint implements OrderTokenValueAwareInterface {
            private ?string $orderTokenValue = null;

            public function getOrderTokenValue(): ?string
            {
                return 'xxx';
            }

            public function setOrderTokenValue(?string $orderTokenValue): void
            {
                $this->orderTokenValue = $orderTokenValue;
            }
        };

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', $constraint])
        ;
    }

    function it_throws_an_exception_if_order_is_null(OrderRepositoryInterface $orderRepository): void
    {
        $constraint = new OrderShippingMethodEligibility();

        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_adds_a_violation_for_every_not_available_shipping_method_attached_to_the_order(
        OrderRepositoryInterface $orderRepository,
        ExecutionContextInterface $context,
        OrderTokenValueAwareInterface $value,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipmentOne,
        ShipmentInterface $shipmentTwo,
        ShippingMethodInterface $shippingMethodOne,
        ShippingMethodInterface $shippingMethodTwo,
        Collection $channelsCollectionOne,
        Collection $channelsCollectionTwo,
    ): void {
        $this->initialize($context);

        $value->getOrderTokenValue()->willReturn('ORDERTOKENVALUE');
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKENVALUE'])->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([$shipmentOne->getWrappedObject(), $shipmentTwo->getWrappedObject()]));
        $order->getChannel()->willReturn($channel);

        $shipmentOne->getMethod()->willReturn($shippingMethodOne);
        $shipmentTwo->getMethod()->willReturn($shippingMethodTwo);

        $shippingMethodOne->isEnabled()->willReturn(false);
        $shippingMethodTwo->isEnabled()->willReturn(true);

        $shippingMethodOne->getChannels()->willReturn($channelsCollectionOne);
        $shippingMethodTwo->getChannels()->willReturn($channelsCollectionTwo);

        $shippingMethodOne->getName()->willReturn('Shipping method one');
        $shippingMethodTwo->getName()->willReturn('Shipping method two');

        $channelsCollectionOne->contains($channel)->willReturn(true);
        $channelsCollectionTwo->contains($channel)->willReturn(false);

        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method one'])->shouldBeCalled();
        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method two'])->shouldBeCalled();

        $this->validate($value, new OrderShippingMethodEligibility());
    }

    function it_does_not_add_violation_if_all_shipping_methods_are_available(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        ExecutionContextInterface $context,
        OrderTokenValueAwareInterface $value,
        ChannelInterface $channel,
        OrderInterface $order,
        ShipmentInterface $shipmentOne,
        ShipmentInterface $shipmentTwo,
        ShippingMethodInterface $shippingMethodOne,
        ShippingMethodInterface $shippingMethodTwo,
        Collection $channelsCollectionOne,
        Collection $channelsCollectionTwo,
    ): void {
        $this->initialize($context);

        $value->getOrderTokenValue()->willReturn('ORDERTOKENVALUE');
        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKENVALUE'])->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([$shipmentOne->getWrappedObject(), $shipmentTwo->getWrappedObject()]));
        $order->getChannel()->willReturn($channel);

        $shipmentOne->getMethod()->willReturn($shippingMethodOne);
        $shipmentTwo->getMethod()->willReturn($shippingMethodTwo);

        $shippingMethodOne->isEnabled()->willReturn(true);
        $shippingMethodTwo->isEnabled()->willReturn(true);

        $shippingMethodOne->getChannels()->willReturn($channelsCollectionOne);
        $shippingMethodTwo->getChannels()->willReturn($channelsCollectionTwo);

        $shippingMethodOne->getName()->willReturn('Shipping method one');
        $shippingMethodTwo->getName()->willReturn('Shipping method two');

        $channelsCollectionOne->contains($channel)->willReturn(true);
        $channelsCollectionTwo->contains($channel)->willReturn(true);

        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method one'])->shouldNotBeCalled();
        $context->addViolation('sylius.order.shipping_method_not_available', ['%shippingMethodName%' => 'Shipping method two'])->shouldNotBeCalled();

        $eligibilityChecker->isEligible($shipmentOne, $shippingMethodOne)->willReturn(true);
        $eligibilityChecker->isEligible($shipmentTwo, $shippingMethodTwo)->willReturn(true);

        $this->validate($value, new OrderShippingMethodEligibility());
    }

    function it_adds_violation_if_shipment_does_not_match_with_shipping_method(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        Collection $channelsCollection,
        ChannelInterface $channel,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderShippingMethodEligibility();

        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $order->getChannel()->willReturn($channel);

        $shipment->getMethod()->willReturn($shippingMethod);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);

        $shippingMethod->getName()->willReturn('InPost');
        $shippingMethod->isEnabled()->willReturn(true);
        $shippingMethod->getChannels()->willReturn($channelsCollection);

        $channelsCollection->contains($channel)->willReturn(true);

        $executionContext
            ->addViolation(
                'sylius.order.shipping_method_eligibility',
                ['%shippingMethodName%' => 'InPost'],
            )
            ->shouldBeCalled()
        ;

        $this->validate($value, $constraint);
    }

    function it_does_not_add_a_violation_if_shipment_matches_with_shipping_method(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        Collection $channelsCollection,
        ChannelInterface $channel,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderShippingMethodEligibility();

        $value = new CompleteOrder(orderTokenValue: 'token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));
        $order->getChannel()->willReturn($channel);

        $shipment->getMethod()->willReturn($shippingMethod);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shippingMethod->getName()->willReturn('InPost');
        $shippingMethod->isEnabled()->willReturn(true);
        $shippingMethod->getChannels()->willReturn($channelsCollection);

        $channelsCollection->contains($channel)->willReturn(true);

        $executionContext
            ->addViolation(
                'sylius.order.shipping_method_eligibility',
                ['%shippingMethodName%' => 'InPost'],
            )
            ->shouldNotBeCalled()
        ;

        $this->validate($value, $constraint);
    }
}
