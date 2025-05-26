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

namespace Tests\Sylius\Component\Core\Distributor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Distributor\MinimumPriceDistributor;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class MinimumPriceDistributorTest extends TestCase
{
    private MockObject&ProportionalIntegerDistributorInterface $proportionalIntegerDistributor;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderItemInterface $tshirt;

    private MockObject&ProductVariantInterface $tshirtVariant;

    private ChannelPricingInterface&MockObject $tshirtVariantChannelPricing;

    private MockObject&OrderItemInterface $book;

    private MockObject&ProductVariantInterface $bookVariant;

    private ChannelPricingInterface&MockObject $bookVariantChannelPricing;

    private MinimumPriceDistributor $distributor;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->tshirt = $this->createMock(OrderItemInterface::class);
        $this->tshirtVariant = $this->createMock(ProductVariantInterface::class);
        $this->tshirtVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->book = $this->createMock(OrderItemInterface::class);
        $this->bookVariant = $this->createMock(ProductVariantInterface::class);
        $this->bookVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->proportionalIntegerDistributor = $this->createMock(ProportionalIntegerDistributorInterface::class);
        $this->distributor = new MinimumPriceDistributor($this->proportionalIntegerDistributor);
    }

    public function testShouldDistributePromotionTakingIntoAccountMinimumPrice(): void
    {
        $shoes = $this->createMock(OrderItemInterface::class);
        $shoesVariant = $this->createMock(ProductVariantInterface::class);
        $shoesVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $boardGame = $this->createMock(OrderItemInterface::class);
        $boardGameVariant = $this->createMock(ProductVariantInterface::class);
        $boardGameVariantChannelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->tshirt->expects($this->atLeastOnce())->method('getTotal')->willReturn(1000);
        $this->tshirt->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->tshirt->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->tshirtVariant);
        $this->tshirtVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->tshirtVariantChannelPricing);
        $this->tshirtVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->book->expects($this->atLeastOnce())->method('getTotal')->willReturn(2000);
        $this->book->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->book->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->bookVariant);
        $this->bookVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->bookVariantChannelPricing);
        $this->bookVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(1900);
        $shoes->expects($this->atLeastOnce())->method('getTotal')->willReturn(5000);
        $shoes->expects($this->once())->method('getQuantity')->willReturn(1);
        $shoes->expects($this->atLeastOnce())->method('getVariant')->willReturn($shoesVariant);
        $shoesVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($shoesVariantChannelPricing);
        $shoesVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(5000);
        $boardGame->expects($this->atLeastOnce())->method('getTotal')->willReturn(3000);
        $boardGame->expects($this->once())->method('getQuantity')->willReturn(1);
        $boardGame->expects($this->atLeastOnce())->method('getVariant')->willReturn($boardGameVariant);
        $boardGameVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($boardGameVariantChannelPricing);
        $boardGameVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(2600);
        $this->proportionalIntegerDistributor->expects($this->exactly(3))->method('distribute')->willReturnMap([
            [[1000, 2000, 5000, 3000], -1200, [-110, -218, -545, -327]],
            [[1000, 3000], -663, [-166, -497]],
            [[1000], -424, [-424]],
        ]);

        $this->assertSame(
            [-700, -100, 0, -400],
            $this->distributor->distribute([$this->tshirt, $this->book, $shoes, $boardGame], -1200, $this->channel, true),
        );
    }

    public function testShouldDistributePromotionTakingIntoAccountMinimumPriceWithQuantity(): void
    {
        $this->tshirt->expects($this->atLeastOnce())->method('getTotal')->willReturn(5000);
        $this->tshirt->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->tshirt->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->tshirtVariant);
        $this->tshirtVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->tshirtVariantChannelPricing);
        $this->tshirtVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(4500);
        $this->book->expects($this->atLeastOnce())->method('getTotal')->willReturn(6000);
        $this->book->expects($this->once())->method('getQuantity')->willReturn(3);
        $this->book->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->bookVariant);
        $this->bookVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->bookVariantChannelPricing);
        $this->bookVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->proportionalIntegerDistributor->expects($this->exactly(2))->method('distribute')->willReturnMap([
            [[5000, 6000], -2500, [-1136, -1364]],
            [[6000], -636, [-636]],
        ]);

        $this->assertSame(
            [-500, -2000],
            $this->distributor->distribute([$this->tshirt, $this->book], -2500, $this->channel, true),
        );
    }

    public function testShouldDistributePromotionThatExceedsPossibleDistributionTakingIntoAccountMinimumPrice(): void
    {
        $this->tshirt->expects($this->atLeastOnce())->method('getTotal')->willReturn(5000);
        $this->tshirt->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->tshirt->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->tshirtVariant);
        $this->tshirtVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->tshirtVariantChannelPricing);
        $this->tshirtVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(4500);
        $this->book->expects($this->atLeastOnce())->method('getTotal')->willReturn(6000);
        $this->book->expects($this->once())->method('getQuantity')->willReturn(3);
        $this->book->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->bookVariant);
        $this->bookVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->bookVariantChannelPricing);
        $this->bookVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(1500);
        $this->proportionalIntegerDistributor->expects($this->exactly(2))->method('distribute')->willReturnMap([
            [[5000, 6000], -2500, [-1136, -1364]],
            [[6000], -636, [-636]],
        ]);

        $this->assertSame(
            [-500, -1500],
            $this->distributor->distribute([$this->tshirt, $this->book], -2500, $this->channel, true),
        );
    }

    public function testShouldDistributePromotionForProductsWithoutPromotionsIfPromotionDoesNotApplyOnCatalogPromotion(): void
    {
        $this->tshirt->expects($this->atLeastOnce())->method('getTotal')->willReturn(5000);
        $this->tshirt->expects($this->once())->method('getQuantity')->willReturn(1);
        $this->tshirt->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->tshirtVariant);
        $this->tshirtVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->tshirtVariantChannelPricing);
        $this->tshirtVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(4500);
        $this->tshirtVariant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([['promotion_applied']]));
        $this->book->expects($this->atLeastOnce())->method('getTotal')->willReturn(6000);
        $this->book->expects($this->once())->method('getQuantity')->willReturn(3);
        $this->book->expects($this->atLeastOnce())->method('getVariant')->willReturn($this->bookVariant);
        $this->bookVariant
            ->expects($this->once())
            ->method('getChannelPricingForChannel')
            ->with($this->channel)
            ->willReturn($this->bookVariantChannelPricing);
        $this->bookVariantChannelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(1500);
        $this->bookVariant
            ->expects($this->once())
            ->method('getAppliedPromotionsForChannel')
            ->with($this->channel)
            ->willReturn(new ArrayCollection([]));
        $this->proportionalIntegerDistributor
            ->expects($this->once())
            ->method('distribute')
            ->with([0, 6000], -2500)
            ->willReturn([0, -1500]);

        $this->assertSame(
            [0, -1500],
            $this->distributor->distribute([$this->tshirt, $this->book], -2500, $this->channel, false),
        );
    }
}
