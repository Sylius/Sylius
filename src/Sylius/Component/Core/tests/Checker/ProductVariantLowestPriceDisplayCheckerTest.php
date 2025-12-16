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

namespace Tests\Sylius\Component\Core\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayChecker;
use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductVariantLowestPriceDisplayCheckerTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private ChannelPriceHistoryConfigInterface&MockObject $channelPriceHistoryConfig;

    private MockObject&ProductInterface $product;

    private MockObject&ProductVariantInterface $productVariant;

    private MockObject&TaxonInterface $firstTaxon;

    private MockObject&TaxonInterface $secondTaxon;

    private ProductVariantLowestPriceDisplayChecker $checker;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->channelPriceHistoryConfig = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $this->product = $this->createMock(ProductInterface::class);
        $this->productVariant = $this->createMock(ProductVariantInterface::class);
        $this->firstTaxon = $this->createMock(TaxonInterface::class);
        $this->secondTaxon = $this->createMock(TaxonInterface::class);
        $this->checker = new ProductVariantLowestPriceDisplayChecker();
    }

    public function testShouldImplementProductVariantLowestPriceCheckerInterface(): void
    {
        $this->assertInstanceOf(ProductVariantLowestPriceDisplayCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnFalseIfChannelHasNoPriceHistoryConfig(): void
    {
        $this->channel->expects($this->once())->method('getChannelPriceHistoryConfig')->willReturn(null);

        $this->assertFalse($this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldReturnFalseIfShowingLowestPriceBeforeDiscountIsTurnedOffOnChannel(): void
    {
        $this->channel->expects($this->once())->method('getChannelPriceHistoryConfig')->willReturn($this->channelPriceHistoryConfig);
        $this->channelPriceHistoryConfig->expects($this->once())->method('isLowestPriceForDiscountedProductsVisible')->willReturn(false);

        $this->assertFalse($this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldReturnTrueIfProductVariantHasNoTaxonsAssigned(): void
    {
        $this->channel->expects($this->once())->method('getChannelPriceHistoryConfig')->willReturn($this->channelPriceHistoryConfig);
        $this->channelPriceHistoryConfig->expects($this->once())->method('isLowestPriceForDiscountedProductsVisible')->willReturn(true);
        $this->productVariant->expects($this->once())->method('getProduct')->willReturn($this->product);
        $this->product->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection());

        $this->assertTrue($this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldReturnTrueIfThereIsNoTaxonsExcludedShowingLowestPriceInChannel(): void
    {
        $this->channel->expects($this->once())->method('getChannelPriceHistoryConfig')->willReturn($this->channelPriceHistoryConfig);
        $this->channelPriceHistoryConfig->expects($this->once())->method('isLowestPriceForDiscountedProductsVisible')->willReturn(true);
        $this->channelPriceHistoryConfig
            ->expects($this->once())
            ->method('getTaxonsExcludedFromShowingLowestPrice')
            ->willReturn(new ArrayCollection());
        $this->productVariant->expects($this->once())->method('getProduct')->willReturn($this->product);
        $this->product->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([$this->firstTaxon]));

        $this->assertTrue($this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldReturnFalseIfAtLeastOneProductVariantsTaxonIsExcludedFromShowingLowestPriceInChannel(): void
    {
        $this->channel->expects($this->once())->method('getChannelPriceHistoryConfig')->willReturn($this->channelPriceHistoryConfig);
        $this->channelPriceHistoryConfig->expects($this->once())->method('isLowestPriceForDiscountedProductsVisible')->willReturn(true);
        $this->productVariant->expects($this->once())->method('getProduct')->willReturn($this->product);
        $this->firstTaxon->expects($this->exactly(4))->method('getCode')->willReturn('first_taxon');
        $this->secondTaxon->expects($this->exactly(2))->method('getCode')->willReturn('second_taxon');
        $this->product->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([
            $this->firstTaxon,
            $this->secondTaxon,
        ]));
        $this->channelPriceHistoryConfig
            ->expects($this->once())
            ->method('getTaxonsExcludedFromShowingLowestPrice')
            ->willReturn(new ArrayCollection([$this->firstTaxon]));

        $this->assertFalse($this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldReturnFalseIfParentOfAtLeastOneProductVariantsTaxonIsExcludedFromShowingLowestPriceInChannel(): void
    {
        $firstTaxonChild = $this->createMock(TaxonInterface::class);
        $childOfFirstTaxonChild = $this->createMock(TaxonInterface::class);
        $this->channel
            ->expects($this->once())
            ->method('getChannelPriceHistoryConfig')
            ->willReturn($this->channelPriceHistoryConfig);
        $this->channelPriceHistoryConfig
            ->expects($this->once())
            ->method('isLowestPriceForDiscountedProductsVisible')
            ->willReturn(true);
        $this->productVariant->expects($this->once())->method('getProduct')->willReturn($this->product);
        $this->firstTaxon->expects($this->atLeastOnce())->method('getCode')->willReturn('first_taxon');
        $this->firstTaxon->expects($this->once())->method('getChildren')->willReturn(new ArrayCollection([$firstTaxonChild]));
        $firstTaxonChild->expects($this->atLeastOnce())->method('getCode')->willReturn('first_taxon_child');
        $firstTaxonChild->expects($this->once())->method('getChildren')->willReturn(new ArrayCollection([$childOfFirstTaxonChild]));
        $childOfFirstTaxonChild->expects($this->atLeastOnce())->method('getCode')->willReturn('child_of_first_taxon_child');
        $this->secondTaxon->expects($this->atLeastOnce())->method('getCode')->willReturn('second_taxon');
        $this->product->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([
                $childOfFirstTaxonChild,
                $this->secondTaxon,
        ]));
        $this->channelPriceHistoryConfig
            ->expects($this->once())
            ->method('getTaxonsExcludedFromShowingLowestPrice')
            ->willReturn(new ArrayCollection([
                $this->firstTaxon,
            ]));

        $this->assertFalse(
            $this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]),
        );
    }

    public function testShouldReturnTrueIfNoneOfTheProductVariantsTaxonsIsExcludedFromShowingLowestPriceInChannel(): void
    {
        $this->channel->expects($this->once())->method('getChannelPriceHistoryConfig')->willReturn($this->channelPriceHistoryConfig);
        $this->channelPriceHistoryConfig->expects($this->once())->method('isLowestPriceForDiscountedProductsVisible')->willReturn(true);
        $this->productVariant->expects($this->once())->method('getProduct')->willReturn($this->product);
        $this->product->expects($this->once())->method('getTaxons')->willReturn(new ArrayCollection([
                $this->firstTaxon,
                $this->secondTaxon,
            ]));
        $this->channelPriceHistoryConfig
            ->expects($this->once())
            ->method('getTaxonsExcludedFromShowingLowestPrice')
            ->willReturn(new ArrayCollection());

        $this->assertTrue($this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => $this->channel]));
    }

    public function testShouldThrowExceptionIfThereIsNoChannelPassedInContext(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->checker->isLowestPriceDisplayable($this->productVariant, []);
    }

    public function testShouldThrowExceptionIfThereIsNoChannelSetUnderTheChannelKeyInContext(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->checker->isLowestPriceDisplayable($this->productVariant, ['channel' => new \stdClass()]);
    }
}
