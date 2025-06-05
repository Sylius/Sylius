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
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailability;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderItemAvailabilityValidator;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class OrderItemAvailabilityValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private AvailabilityCheckerInterface&MockObject $availabilityChecker;

    private ExecutionContextInterface&MockObject $executionContext;

    private OrderItemAvailabilityValidator $orderItemAvailabilityValidator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->availabilityChecker = $this->createMock(AvailabilityCheckerInterface::class);
        $this->executionContext = $this->createMock(ExecutionContextInterface::class);
        $this->orderItemAvailabilityValidator = new OrderItemAvailabilityValidator($this->orderRepository, $this->availabilityChecker);
        $this->orderItemAvailabilityValidator->initialize($this->executionContext);
    }

    public function testAConstraintValidator(): void
    {
        self::assertInstanceOf(ConstraintValidatorInterface::class, $this->orderItemAvailabilityValidator);
    }

    public function testThrowsExceptionIfConstraintIsNotAnInstanceOfOrderProductInStockEligibility(): void
    {
        self::expectException(\InvalidArgumentException::class);

        $invalidConstraint = $this->createMock(Constraint::class);

        $this->orderItemAvailabilityValidator->validate(
            new CompleteOrder('TOKEN'),
            $invalidConstraint,
        );
    }

    public function testAddsViolationIfProductVariantDoesNotHaveSufficientStock(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var Collection|MockObject $orderItemsMock */
        $orderItemsMock = $this->createMock(Collection::class);
        $command = new CompleteOrder(orderTokenValue: 'cartToken');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cartToken'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getItems')->willReturn($orderItemsMock);
        $orderItemsMock->expects(self::once())->method('getIterator')->willReturn(new ArrayCollection([$orderItemMock]));
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($productVariantMock);
        $orderItemMock->expects(self::once())->method('getQuantity')->willReturn(1);
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 1)->willReturn(false);
        $productVariantMock->expects(self::once())->method('getName')->willReturn('variant name');
        $this->executionContext->expects(self::once())->method('addViolation')->with('sylius.product_variant.product_variant_with_name_not_sufficient', ['%productVariantName%' => 'variant name'])
        ;
        $this->orderItemAvailabilityValidator->validate($command, new OrderItemAvailability());
    }

    public function testDoesNothingIfProductVariantHasSufficientStock(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var OrderItemInterface|MockObject $orderItemMock */
        $orderItemMock = $this->createMock(OrderItemInterface::class);
        /** @var ProductVariantInterface|MockObject $productVariantMock */
        $productVariantMock = $this->createMock(ProductVariantInterface::class);
        /** @var Collection|MockObject $orderItemsMock */
        $orderItemsMock = $this->createMock(Collection::class);
        $command = new CompleteOrder(orderTokenValue: 'cartToken');
        $this->orderRepository->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cartToken'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getItems')->willReturn($orderItemsMock);
        $orderItemsMock->expects(self::once())->method('getIterator')->willReturn(new ArrayCollection([$orderItemMock]));
        $orderItemMock->expects(self::once())->method('getVariant')->willReturn($productVariantMock);
        $orderItemMock->expects(self::once())->method('getQuantity')->willReturn(1);
        $this->availabilityChecker->expects(self::once())->method('isStockSufficient')->with($productVariantMock, 1)->willReturn(true);
        $productVariantMock->expects(self::never())->method('getName');
        $this->executionContext->expects(self::never())->method('addViolation')->with('sylius.product_variant.product_variant_with_name_not_sufficient', ['%productVariantName%' => 'variant name'])
        ;
        $this->orderItemAvailabilityValidator->validate($command, new OrderItemAvailability());
    }
}
