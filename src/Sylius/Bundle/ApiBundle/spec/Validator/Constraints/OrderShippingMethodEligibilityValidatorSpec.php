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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\ChooseShippingMethod;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderShippingMethodEligibility;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderShippingMethodEligibilityValidatorSpec extends ObjectBehavior
{
    function let(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker
    ): void {
        $this->beConstructedWith(
            $shipmentRepository,
            $shippingMethodRepository,
            $eligibilityChecker
        );
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_an_exception_if_constraint_does_not_extend_order_token_value_aware_interface(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', ['', new class() extends Constraint {}])
        ;
    }

    function it_throws_an_exception_if_constraint_does_not_type_of_order_shipping_method_eligibility(): void {
        $constraint = new class() extends Constraint implements OrderTokenValueAwareInterface{
            private $orderTokenValue;

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

    function it_throws_an_exception_if_shipment_is_null(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository
    ): void {
        $constraint = new OrderShippingMethodEligibility();

        $value = new ChooseShippingMethod('xxx');
        $value->shipmentId = '20';

        $shipmentRepository->findOneBy(['id' => '20'])->willReturn(null);
        $shippingMethodRepository->findOneBy(['code' => 'xxx'])->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_throws_an_exception_if_shipping_method_is_null(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        ShipmentInterface $shipment
    ): void {
        $constraint = new OrderShippingMethodEligibility();

        $value = new ChooseShippingMethod('xxx');
        $value->shipmentId = '20';

        $shipmentRepository->findOneBy(['id' => '20'])->willReturn($shipment);
        $shippingMethodRepository->findOneBy(['code' => 'xxx'])->willReturn(null);
        $eligibilityChecker->isEligible($shipment, null)->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$value, $constraint])
        ;
    }

    function it_add_violation_if_shipment_does_not_match_with_shipping_method(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderShippingMethodEligibility();

        $value = new ChooseShippingMethod('xxx');
        $value->shipmentId = '20';

        $shipmentRepository->findOneBy(['id' => '20'])->willReturn($shipment);
        $shippingMethodRepository->findOneBy(['code' => 'xxx'])->willReturn($shippingMethod);
        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(false);

        $shippingMethod->getName()->willReturn('InPost');

        $executionContext
            ->addViolation(
                "sylius.order.shipping_method_eligibility",
                ["%shippingMethodName%" => "InPost"]
            )
            ->shouldBeCalled()
        ;

        $this->validate($value, $constraint);
    }

    function it_does_not_add_a_violation_if_shipment_match_with_shipping_method(
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodRepository,
        ShippingMethodEligibilityCheckerInterface $eligibilityChecker,
        ShipmentInterface $shipment,
        ShippingMethodInterface $shippingMethod,
        ExecutionContextInterface $executionContext
    ): void {
        $this->initialize($executionContext);

        $constraint = new OrderShippingMethodEligibility();

        $value = new ChooseShippingMethod('xxx');
        $value->shipmentId = '20';

        $shipmentRepository->findOneBy(['id' => '20'])->willReturn($shipment);
        $shippingMethodRepository->findOneBy(['code' => 'xxx'])->willReturn($shippingMethod);
        $eligibilityChecker->isEligible($shipment, $shippingMethod)->willReturn(true);

        $executionContext
            ->addViolation(
                "sylius.order.shipping_method_eligibility",
                ["%shippingMethodName%" => "InPost"]
            )
            ->shouldNotBeCalled()
        ;

        $this->validate($value, $constraint);
    }
}
