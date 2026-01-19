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

namespace Tests\Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ChannelPricingTest extends TestCase
{
    private CatalogPromotionInterface&MockObject $catalogPromotion;

    private ChannelPricing $channelPricing;

    protected function setUp(): void
    {
        $this->catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $this->channelPricing = new ChannelPricing();
    }

    public function testShouldImplementChannelPricingInterface(): void
    {
        $this->assertInstanceOf(ChannelPricingInterface::class, $this->channelPricing);
    }

    public function testShouldChannelCodeBeMutable(): void
    {
        $this->channelPricing->setChannelCode('WEB');

        $this->assertSame('WEB', $this->channelPricing->getChannelCode());
    }

    public function testShouldProductVariantBeMutable(): void
    {
        $productVariant = $this->createMock(ProductVariantInterface::class);

        $this->channelPricing->setProductVariant($productVariant);

        $this->assertSame($productVariant, $this->channelPricing->getProductVariant());
    }

    public function testShouldPriceBeMutable(): void
    {
        $this->channelPricing->setPrice(1000);

        $this->assertSame(1000, $this->channelPricing->getPrice());
    }

    public function testShouldNotHaveOriginalPriceByDefault(): void
    {
        $this->assertNull($this->channelPricing->getOriginalPrice());
    }

    public function testShouldOriginalPriceBeMutable(): void
    {
        $this->channelPricing->setOriginalPrice(2000);

        $this->assertSame(2000, $this->channelPricing->getOriginalPrice());
    }

    public function testShouldLowestPriceBeforeDiscountBeMutable(): void
    {
        $this->channelPricing->setLowestPriceBeforeDiscount(2000);

        $this->assertSame(2000, $this->channelPricing->getLowestPriceBeforeDiscount());
    }

    public function testShouldPriceBeAbleToReduce(): void
    {
        $this->channelPricing->setPrice(1000);
        $this->channelPricing->setOriginalPrice(2000);

        $this->assertTrue($this->channelPricing->isPriceReduced());
    }

    public function testShouldPriceBeNotReducedIfOriginalPriceIsNotSet(): void
    {
        $this->channelPricing->setPrice(2000);
        $this->assertFalse($this->channelPricing->isPriceReduced());
    }

    public function testShouldPriceNotBeReducedIfOriginalPriceIsSameAsPrice(): void
    {
        $this->channelPricing->setPrice(2000);
        $this->channelPricing->setOriginalPrice(2000);

        $this->assertFalse($this->channelPricing->isPriceReduced());
    }

    public function testShouldPriceNotBeReducedIfOriginalPriceIsSmallerThanPrice(): void
    {
        $this->channelPricing->setPrice(2000);
        $this->channelPricing->setOriginalPrice(1500);

        $this->assertFalse($this->channelPricing->isPriceReduced());
    }

    public function testShouldInitializeCatalogPromotionsCollectionByDefault(): void
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->channelPricing->getAppliedPromotions());
    }

    public function testShouldHaveInformationAboutAppliedExclusiveCatalogPromotionApplied(): void
    {
        $this->catalogPromotion->expects($this->once())->method('isExclusive')->willReturn(true);

        $this->channelPricing->addAppliedPromotion($this->catalogPromotion);

        $this->assertTrue($this->channelPricing->hasExclusiveCatalogPromotionApplied());
    }

    public function testShouldBeAbleToHaveMultiplePromotionsApplied(): void
    {
        $secondCatalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->channelPricing->addAppliedPromotion($this->catalogPromotion);
        $this->channelPricing->addAppliedPromotion($secondCatalogPromotion);

        $this->assertEquals(
            new ArrayCollection([$this->catalogPromotion, $secondCatalogPromotion]),
            $this->channelPricing->getAppliedPromotions(),
        );
    }

    public function testShouldRemoveAppliedPromotion(): void
    {
        $this->channelPricing->addAppliedPromotion($this->catalogPromotion);

        $this->channelPricing->removeAppliedPromotion($this->catalogPromotion);

        $this->assertFalse($this->channelPricing->hasPromotionApplied($this->catalogPromotion));
    }

    public function testShouldClearAppliedPromotions(): void
    {
        $this->channelPricing->addAppliedPromotion($this->catalogPromotion);

        $this->channelPricing->clearAppliedPromotions();

        $this->assertSame(0, $this->channelPricing->getAppliedPromotions()->count());
    }

    public function testShouldCheckIfGivenCatalogPromotionIsApplied(): void
    {
        $this->channelPricing->addAppliedPromotion($this->catalogPromotion);

        $this->assertTrue($this->channelPricing->hasPromotionApplied($this->catalogPromotion));
    }

    public function testShouldCheckIfGivenCatalogPromotionIsNotApplied(): void
    {
        $this->assertFalse($this->channelPricing->hasPromotionApplied($this->catalogPromotion));
    }
}
