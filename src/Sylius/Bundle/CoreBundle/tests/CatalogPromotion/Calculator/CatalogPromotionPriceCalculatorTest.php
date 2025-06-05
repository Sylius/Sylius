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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class CatalogPromotionPriceCalculatorTest extends TestCase
{
    private ActionBasedPriceCalculatorInterface&MockObject $fixedDiscountCalculator;

    private ActionBasedPriceCalculatorInterface&MockObject $percentageDiscountCalculator;

    private CatalogPromotionPriceCalculator $catalogPromotionPriceCalculator;

    protected function setUp(): void
    {
        $this->fixedDiscountCalculator = $this->createMock(ActionBasedPriceCalculatorInterface::class);
        $this->percentageDiscountCalculator = $this->createMock(ActionBasedPriceCalculatorInterface::class);
        $this->catalogPromotionPriceCalculator = new CatalogPromotionPriceCalculator([$this->fixedDiscountCalculator, $this->percentageDiscountCalculator]);
    }

    public function testImplementsCatalogPromotionPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionPriceCalculatorInterface::class, $this->catalogPromotionPriceCalculator);
    }

    public function testCalculatesPriceOfChannelPricingForGivenActionByAProperCalculator(): void
    {
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->fixedDiscountCalculator
            ->expects($this->once())
            ->method('supports')
            ->with($action)
            ->willReturn(false)
        ;

        $this->percentageDiscountCalculator
            ->expects($this->once())
            ->method('supports')
            ->with($action)
            ->willReturn(true)
        ;

        $this->percentageDiscountCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($channelPricing, $action)
            ->willReturn(1000)
        ;

        $this->assertSame(1000, $this->catalogPromotionPriceCalculator->calculate($channelPricing, $action));
    }

    public function testThrowsAnExceptionIfThereIsNoCalculatorThatSupportsGivenAction(): void
    {
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->fixedDiscountCalculator
            ->expects($this->once())
            ->method('supports')
            ->with($action)
            ->willReturn(false)
        ;

        $this->percentageDiscountCalculator
            ->expects($this->once())
            ->method('supports')
            ->with($action)
            ->willReturn(false)
        ;

        $this->expectException(ActionBasedPriceCalculatorNotFoundException::class);

        $this->catalogPromotionPriceCalculator->calculate($channelPricing, $action);
    }
}
