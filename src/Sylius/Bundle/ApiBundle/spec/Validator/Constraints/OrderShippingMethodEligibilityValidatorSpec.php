<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibility;
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

        $value = new CompleteOrder();
        $value->setOrderTokenValue('token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_adds_violation_if_shipment_does_not_match_with_shipping_method(
        OrderRepositoryInterface $orderRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        OrderInterface $order,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderShippingMethodEligibility();

        $value = new CompleteOrder();
        $value->setOrderTokenValue('token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $shipment->getMethod()->willReturn($shippingMethod);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);

        $shippingMethod->getName()->willReturn('InPost');

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
        ExecutionContextInterface $executionContext,
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderShippingMethodEligibility();

        $value = new CompleteOrder();
        $value->setOrderTokenValue('token');

        $orderRepository->findOneBy(['tokenValue' => 'token'])->willReturn($order);

        $order->getShipments()->willReturn(new ArrayCollection([$shipment->getWrappedObject()]));

        $shipment->getMethod()->willReturn($shippingMethod);

        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $shippingMethod->getName()->willReturn('InPost');

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
