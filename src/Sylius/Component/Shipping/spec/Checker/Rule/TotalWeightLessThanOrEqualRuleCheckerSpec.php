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

namespace spec\Sylius\Component\Shipping\Checker\Rule;

use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class TotalWeightLessThanOrEqualRuleCheckerSpec extends ObjectBehavior
{
    public function it_implements_rule_checker_interface(): void
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    public function it_throws_exception_if_subject_is_not_a_shipment(ShippingSubjectInterface $subject): void
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('isEligible', [$subject, []]);
    }

    public function it_recognizes_subject_if_order_is_null(ShipmentInterface $shipment): void
    {
        $shipment->getOrder()->willReturn(null);

        $this->isEligible($shipment, [])->shouldReturn(true);
    }

    public function it_recognizes_subject_if_total_weight_is_less_than_configured_weight(
        ShipmentInterface $subject,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    ): void {
        $subject->getOrder()->willReturn($order);

        $shipment1->getShippingWeight()->willReturn(1);
        $shipment2->getShippingWeight()->willReturn(2);

        $order->getShipments()->willReturn(
            new ArrayCollection([
                $shipment1->getWrappedObject(),
                $shipment2->getWrappedObject(),
            ])
        );

        $this->isEligible($subject, ['weight' => 5])->shouldReturn(true);
    }

    public function it_recognizes_subject_if_total_weight_is_equal_to_configured_weight(
        ShipmentInterface $subject,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    ): void {
        $subject->getOrder()->willReturn($order);

        $shipment1->getShippingWeight()->willReturn(3.55);
        $shipment2->getShippingWeight()->willReturn(1.45);

        $order->getShipments()->willReturn(
            new ArrayCollection([
                $shipment1->getWrappedObject(),
                $shipment2->getWrappedObject(),
            ])
        );

        $this->isEligible($subject, ['weight' => 5])->shouldReturn(true);
    }

    public function it_denies_subject_if_total_weight_is_greater_than_configured_weight(
        ShipmentInterface $subject,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2
    ): void {
        $subject->getOrder()->willReturn($order);

        $shipment1->getShippingWeight()->willReturn(3.5);
        $shipment2->getShippingWeight()->willReturn(2.45);

        $order->getShipments()->willReturn(
            new ArrayCollection([
                $shipment1->getWrappedObject(),
                $shipment2->getWrappedObject(),
            ])
        );

        $this->isEligible($subject, ['weight' => 5])->shouldReturn(false);
    }
}
