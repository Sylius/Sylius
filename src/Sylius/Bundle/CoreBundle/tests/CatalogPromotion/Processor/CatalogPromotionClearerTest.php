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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class CatalogPromotionClearerTest extends TestCase
{
    private CatalogPromotionClearerInterface $clearer;

    protected function setUp(): void
    {
        $this->clearer = new CatalogPromotionClearer();
    }

    public function testImplementsCatalogPromotionClearerInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionClearerInterface::class, $this->clearer);
    }

    public function testClearsGivenVariantWithCatalogPromotionsApplied(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);
        $firstChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $secondChannelPricing = $this->createMock(ChannelPricingInterface::class);
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $variant->method('getChannelPricings')->willReturn(new ArrayCollection([
            $firstChannelPricing,
            $secondChannelPricing,
        ]));

        $firstChannelPricing
            ->method('getAppliedPromotions')
            ->willReturn(new ArrayCollection([$catalogPromotion]))
        ;

        $firstChannelPricing->method('getOriginalPrice')->willReturn(1000);
        $firstChannelPricing->expects($this->once())->method('setPrice')->with(1000);
        $firstChannelPricing->expects($this->once())->method('clearAppliedPromotions');

        $secondChannelPricing->method('getAppliedPromotions')->willReturn(new ArrayCollection());
        $secondChannelPricing->expects($this->never())->method('getOriginalPrice');
        $secondChannelPricing->expects($this->never())->method('clearAppliedPromotions');

        $this->clearer->clearVariant($variant);
    }
}
