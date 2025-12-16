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
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Comparable;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductImageInterface;
use Sylius\Component\Core\Model\ProductImagesAwareInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductVariant as BaseProductVariant;
use Sylius\Component\Shipping\Model\ShippableInterface;
use Sylius\Component\Shipping\Model\ShippingCategoryInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Resource\Model\VersionedInterface;

final class ProductVariantTest extends TestCase
{
    private MockObject&TaxCategoryInterface $taxCategory;

    private MockObject&ShippingCategoryInterface $shippingCategory;

    private ChannelPricingInterface&MockObject $channelPricing;

    private ChannelInterface&MockObject $channel;

    private MockObject&ProductImageInterface $image;

    private ProductVariant $productVariant;

    protected function setUp(): void
    {
        $this->taxCategory = $this->createMock(TaxCategoryInterface::class);
        $this->shippingCategory = $this->createMock(ShippingCategoryInterface::class);
        $this->channelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->image = $this->createMock(ProductImageInterface::class);
        $this->productVariant = new ProductVariant();
    }

    public function testShouldImplementProductVariantInterface(): void
    {
        $this->assertInstanceOf(ProductVariantInterface::class, $this->productVariant);
    }

    public function testShouldImplementTaxableInterface(): void
    {
        $this->assertInstanceOf(TaxableInterface::class, $this->productVariant);
    }

    public function testShouldImplementDoctrineComparable(): void
    {
        $this->assertInstanceOf(Comparable::class, $this->productVariant);
    }

    public function testShouldExtendProductVariantModel(): void
    {
        $this->assertInstanceOf(BaseProductVariant::class, $this->productVariant);
    }

    public function testShouldImplementShippableInterface(): void
    {
        $this->assertInstanceOf(ShippableInterface::class, $this->productVariant);
    }

    public function testShouldImplementVersionedInterface(): void
    {
        $this->assertInstanceOf(VersionedInterface::class, $this->productVariant);
    }

    public function testShouldImplementProductImagesAwareInterface(): void
    {
        $this->assertInstanceOf(ProductImagesAwareInterface::class, $this->productVariant);
    }

    public function testShouldHaveVersionOneByDefault(): void
    {
        $this->assertSame(1, $this->productVariant->getVersion());
    }

    public function testShouldHaveNoWeightByDefault(): void
    {
        $this->assertNull($this->productVariant->getWeight());
    }

    public function testShouldWeightBeMutable(): void
    {
        $this->productVariant->setWeight(120.0);

        $this->assertSame(120.0, $this->productVariant->getWeight());
    }

    public function testShouldHaveNoWidthByDefault(): void
    {
        $this->assertNull($this->productVariant->getWidth());
    }

    public function testShouldWidthBeMutable(): void
    {
        $this->productVariant->setWidth(15.0);

        $this->assertSame(15.0, $this->productVariant->getWidth());
    }

    public function testShouldHasNoHeightByDefault(): void
    {
        $this->assertNull($this->productVariant->getHeight());
    }

    public function testShouldHeightIsMutable(): void
    {
        $this->productVariant->setHeight(40.00);

        $this->assertSame(40.00, $this->productVariant->getHeight());
    }

    public function testShouldReturnsCorrectShippingWeight(): void
    {
        $this->productVariant->setWeight(140.00);

        $this->assertSame(140.00, $this->productVariant->getShippingWeight());
    }

    public function testShouldReturnCorrectShippingVolume(): void
    {
        $this->productVariant->setWidth(10.00);
        $this->productVariant->setHeight(20.00);
        $this->productVariant->setDepth(10.00);

        $this->assertSame(2000.00, $this->productVariant->getShippingVolume());
    }

    public function testShouldReturnsCorrectShippingWidth(): void
    {
        $this->productVariant->setWidth(100.00);

        $this->assertSame(100.00, $this->productVariant->getShippingWidth());
    }

    public function testShouldReturnsCorrectShippingHeight(): void
    {
        $this->productVariant->setHeight(110.00);

        $this->assertSame(110.00, $this->productVariant->getShippingHeight());
    }

    public function testShouldHaveNoCodeByDefault(): void
    {
        $this->assertNull($this->productVariant->getCode());
    }

    public function testShouldCodeBeMutable(): void
    {
        $this->productVariant->setCode('dummy-sku123');

        $this->assertSame('dummy-sku123', $this->productVariant->getCode());
    }

    public function testShouldNotHaveTaxCategoryByDefault(): void
    {
        $this->assertNull($this->productVariant->getTaxCategory());
    }

    public function testShouldAllowSettingTaxCategory(): void
    {
        $this->productVariant->setTaxCategory($this->taxCategory);

        $this->assertSame($this->taxCategory, $this->productVariant->getTaxCategory());
    }

    public function testShouldAllowResettingTaxCategory(): void
    {
        $this->productVariant->setTaxCategory($this->taxCategory);

        $this->productVariant->setTaxCategory(null);

        $this->assertNull($this->productVariant->getTaxCategory());
    }

    public function testShouldHaveNoShippingCategoryByDefault(): void
    {
        $this->assertNull($this->productVariant->getShippingCategory());
    }

    public function testShouldShippingCategoryBeMutable(): void
    {
        $this->productVariant->setShippingCategory($this->shippingCategory);

        $this->assertSame($this->shippingCategory, $this->productVariant->getShippingCategory());
    }

    public function testShouldAddChannelPricing(): void
    {
        $this->channelPricing->expects($this->once())->method('getChannelCode')->willReturn('WEB');
        $this->channelPricing->expects($this->once())->method('setProductVariant')->with($this->productVariant);

        $this->productVariant->addChannelPricing($this->channelPricing);

        $this->assertTrue($this->productVariant->hasChannelPricing($this->channelPricing));
    }

    public function testShouldRemoveChannelPricing(): void
    {
        $this->channelPricing->expects($this->exactly(2))->method('getChannelCode')->willReturn('WEB');
        $this->productVariant->addChannelPricing($this->channelPricing);
        $this->channelPricing->expects($this->once())->method('setProductVariant')->with(null);
        $this->productVariant->removeChannelPricing($this->channelPricing);

        $this->assertFalse($this->productVariant->hasChannelPricing($this->channelPricing));
    }

    public function testShouldHasChannelPricingCollection(): void
    {
        $secondChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $this->channelPricing->expects($this->once())->method('getChannelCode')->willReturn('WEB');
        $secondChannelPricing->expects($this->once())->method('getChannelCode')->willReturn('MOB');
        $this->channelPricing->expects($this->once())->method('setProductVariant')->with($this->productVariant);
        $secondChannelPricing->expects($this->once())->method('setProductVariant')->with($this->productVariant);

        $this->productVariant->addChannelPricing($this->channelPricing);
        $this->productVariant->addChannelPricing($secondChannelPricing);

        $this->assertEquals(
            new ArrayCollection([
                    'WEB' => $this->channelPricing,
                    'MOB' => $secondChannelPricing,
            ]),
            $this->productVariant->getChannelPricings(),
        );
    }

    public function testShouldChecksIfContainsChannelPricingForGivenChannel(): void
    {
        $secondChannel = $this->createMock(ChannelInterface::class);
        $this->channelPricing->expects($this->once())->method('getChannelCode')->willReturn('WEB');
        $this->channel->expects($this->exactly(2))->method('getCode')->willReturn('WEB');
        $secondChannel->expects($this->once())->method('getCode')->willReturn('MOB');
        $this->channelPricing->expects($this->once())->method('setProductVariant')->with($this->productVariant);
        $this->productVariant->addChannelPricing($this->channelPricing);

        $this->assertTrue($this->productVariant->hasChannelPricingForChannel($this->channel));
        $this->assertFalse($this->productVariant->hasChannelPricingForChannel($secondChannel));
    }

    public function testShouldReturnChannelPricingForGivenChannel(): void
    {
        $this->channelPricing->expects($this->once())->method('getChannelCode')->willReturn('WEB');
        $this->channel->expects($this->exactly(2))->method('getCode')->willReturn('WEB');
        $this->channelPricing->expects($this->once())->method('setProductVariant')->with($this->productVariant);
        $this->productVariant->addChannelPricing($this->channelPricing);

        $this->assertSame($this->channelPricing, $this->productVariant->getChannelPricingForChannel($this->channel));
    }

    public function testShouldRequireShippingByDefault(): void
    {
        $this->assertTrue($this->productVariant->isShippingRequired());
    }

    public function testShouldShippingBeMutable(): void
    {
        $this->productVariant->setShippingRequired(false);

        $this->assertFalse($this->productVariant->isShippingRequired());
    }

    public function testShouldInitializeImageCollectionByDefault(): void
    {
        $this->assertInstanceOf(Collection::class, $this->productVariant->getImages());
    }

    public function testShouldAddImage(): void
    {
        $this->productVariant->addImage($this->image);

        $this->assertTrue($this->productVariant->hasImages());
        $this->assertTrue($this->productVariant->hasImage($this->image));
    }

    public function testShouldRemoveImage(): void
    {
        $this->productVariant->addImage($this->image);

        $this->productVariant->removeImage($this->image);

        $this->assertFalse($this->productVariant->hasImages());
        $this->assertFalse($this->productVariant->hasImage($this->image));
    }

    public function testShouldReturnImagesByType(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $this->image->expects($this->once())->method('getType')->willReturn('thumbnail');
        $this->image->expects($this->once())->method('setOwner')->with($product);
        $this->image->expects($this->once())->method('addProductVariant')->with($this->productVariant);

        $this->productVariant->setProduct($product);
        $this->productVariant->addImage($this->image);

        $this->assertEquals(
            new ArrayCollection([$this->image]),
            $this->productVariant->getImagesByType('thumbnail'),
        );
    }

    public function testShouldReturnChannelPricingAppliedPromotions(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $this->channel->expects($this->exactly(2))->method('getCode')->willReturn('WEB');
        $this->channelPricing->expects($this->once())->method('setProductVariant')->with($this->productVariant);
        $this->channelPricing->expects($this->once())->method('getChannelCode')->willReturn('WEB');
        $this->channelPricing->expects($this->once())->method('getAppliedPromotions')->willReturn(new ArrayCollection([
            $catalogPromotion,
        ]));

        $this->productVariant->addChannelPricing($this->channelPricing);

        $this->assertEquals(
            new ArrayCollection([$catalogPromotion]),
            $this->productVariant->getAppliedPromotionsForChannel($this->channel),
        );
    }

    public function testShouldBeComparable(): void
    {
        $otherProductVariant = $this->createMock(ProductVariantInterface::class);
        $otherProductVariant
            ->expects($this->exactly(2))
            ->method('getCode')
            ->willReturnOnConsecutiveCalls('test', 'other');
        $this->productVariant->setCode('test');

        $this->assertSame(0, $this->productVariant->compareTo($otherProductVariant));
        $this->assertSame(1, $this->productVariant->compareTo($otherProductVariant));
    }
}
