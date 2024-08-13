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
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailability;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderItemAvailabilityValidatorSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
        ExecutionContextInterface $executionContext,
    ): void {
        $this->beConstructedWith($orderRepository, $availabilityChecker);
        $this->initialize($executionContext);
    }

    function it_is_a_constraint_validator(): void
    {
        $this->shouldImplement(ConstraintValidatorInterface::class);
    }

    function it_throws_exception_if_constraint_is_not_an_instance_of_order_product_in_stock_eligibility(): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [
                new CompleteOrder(),
                new class() extends Constraint {
                },
            ])
        ;
    }

    function it_adds_violation_if_product_variant_does_not_have_sufficient_stock(
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        Collection $orderItems,
    ): void {
        $command = new CompleteOrder(orderTokenValue: 'cartToken');

        $orderRepository->findOneBy(['tokenValue' => 'cartToken'])->willReturn($order);

        $order->getItems()->willReturn($orderItems->getWrappedObject());

        $orderItems->getIterator()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(1);
        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(false);

        $productVariant->getName()->willReturn('variant name');

        $executionContext
            ->addViolation('sylius.product_variant.product_variant_with_name_not_sufficient', ['%productVariantName%' => 'variant name'])
            ->shouldBeCalled()
        ;

        $this->validate($command, new OrderItemAvailability());
    }

    function it_does_nothing_if_product_variant_has_sufficient_stock(
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker,
        ExecutionContextInterface $executionContext,
        OrderInterface $order,
        OrderItemInterface $orderItem,
        ProductVariantInterface $productVariant,
        Collection $orderItems,
    ): void {
        $command = new CompleteOrder(orderTokenValue: 'cartToken');

        $orderRepository->findOneBy(['tokenValue' => 'cartToken'])->willReturn($order);

        $order->getItems()->willReturn($orderItems->getWrappedObject());

        $orderItems->getIterator()->willReturn(new ArrayCollection([$orderItem->getWrappedObject()]));

        $orderItem->getVariant()->willReturn($productVariant);
        $orderItem->getQuantity()->willReturn(1);
        $availabilityChecker->isStockSufficient($productVariant, 1)->willReturn(true);

        $productVariant->getName()->shouldNotBeCalled();

        $executionContext
            ->addViolation('sylius.product_variant.product_variant_with_name_not_sufficient', ['%productVariantName%' => 'variant name'])
            ->shouldNotBeCalled()
        ;

        $this->validate($command, new OrderItemAvailability());
    }
}
