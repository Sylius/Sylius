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

namespace Sylius\Bundle\CoreBundle\Tests\Provider;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Calculator\ProductVariantPricesCalculatorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Provider\ProductVariantsPricesProvider;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

/** @deprecated since Sylius 1.13 and will be removed in Sylius 2.0. */
final class ProductVariantsPricesProviderTest extends TestCase
{
    private ProductVariantPricesCalculatorInterface&MockObject $productVariantPriceCalculator;

    protected function setUp(): void
    {
        $this->productVariantPriceCalculator = $this->createMock(ProductVariantPricesCalculatorInterface::class);
    }

    public function testProvidesArrayContainingProductVariantOptionsMapWithCorrespondingPriceAndAppliedPromotionsButWithoutLowestPrice(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $tShirt = $this->createMock(ProductInterface::class);
        $black = $this->createMock(ProductOptionValueInterface::class);
        $large = $this->createMock(ProductOptionValueInterface::class);
        $small = $this->createMock(ProductOptionValueInterface::class);
        $white = $this->createMock(ProductOptionValueInterface::class);
        $blackLargeTShirt = $this->createMock(ProductVariantInterface::class);
        $blackSmallTShirt = $this->createMock(ProductVariantInterface::class);
        $whiteLargeTShirt = $this->createMock(ProductVariantInterface::class);
        $whiteSmallTShirt = $this->createMock(ProductVariantInterface::class);
        $winterCatalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $summerCatalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $tShirt->method('getEnabledVariants')->willReturn(new ArrayCollection([
            $blackSmallTShirt,
            $whiteSmallTShirt,
            $blackLargeTShirt,
            $whiteLargeTShirt,
        ]));

        $blackSmallTShirt->method('getAppliedPromotionsForChannel')->willReturn(new ArrayCollection([$winterCatalogPromotion]));
        $whiteSmallTShirt->method('getAppliedPromotionsForChannel')->willReturn(new ArrayCollection());
        $blackLargeTShirt->method('getAppliedPromotionsForChannel')->willReturn(new ArrayCollection([$summerCatalogPromotion]));
        $whiteLargeTShirt->method('getAppliedPromotionsForChannel')->willReturn(new ArrayCollection());

        $blackSmallTShirt->method('getOptionValues')->willReturn(new ArrayCollection([$black, $small]));
        $whiteSmallTShirt->method('getOptionValues')->willReturn(new ArrayCollection([$white, $small]));
        $blackLargeTShirt->method('getOptionValues')->willReturn(new ArrayCollection([$black, $large]));
        $whiteLargeTShirt->method('getOptionValues')->willReturn(new ArrayCollection([$white, $large]));

        $this->productVariantPriceCalculator->method('calculate')->will($this->returnValueMap([
            [$blackSmallTShirt, ['channel' => $channel], 1000],
            [$whiteSmallTShirt, ['channel' => $channel], 1500],
            [$blackLargeTShirt, ['channel' => $channel], 2000],
            [$whiteLargeTShirt, ['channel' => $channel], 2500],
        ]));
        $this->productVariantPriceCalculator->method('calculateOriginal')->will($this->returnValueMap([
            [$blackSmallTShirt, ['channel' => $channel], 1000],
            [$whiteSmallTShirt, ['channel' => $channel], 2000],
            [$blackLargeTShirt, ['channel' => $channel], 2000],
            [$whiteLargeTShirt, ['channel' => $channel], 3000],
        ]));

        $black->method('getOptionCode')->willReturn('t_shirt_color');
        $white->method('getOptionCode')->willReturn('t_shirt_color');
        $small->method('getOptionCode')->willReturn('t_shirt_size');
        $large->method('getOptionCode')->willReturn('t_shirt_size');

        $black->method('getCode')->willReturn('black');
        $white->method('getCode')->willReturn('white');
        $small->method('getCode')->willReturn('small');
        $large->method('getCode')->willReturn('large');

        $provider = new ProductVariantsPricesProvider($this->productVariantPriceCalculator);

        $result = $provider->provideVariantsPrices($tShirt, $channel);

        $this->assertEquals([
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'small',
                'value' => 1000,
                'applied_promotions' => [$winterCatalogPromotion],
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'small',
                'value' => 1500,
                'original-price' => 2000,
            ],
            [
                't_shirt_color' => 'black',
                't_shirt_size' => 'large',
                'value' => 2000,
                'applied_promotions' => [$summerCatalogPromotion],
            ],
            [
                't_shirt_color' => 'white',
                't_shirt_size' => 'large',
                'value' => 2500,
                'original-price' => 3000,
            ],
        ], $result);
    }
}
