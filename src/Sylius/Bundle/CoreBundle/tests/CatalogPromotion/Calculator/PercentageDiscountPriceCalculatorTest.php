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

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class PercentageDiscountPriceCalculatorTest extends TestCase
{
    private PercentageDiscountPriceCalculator $percentageDiscountPriceCalculator;

    protected function setUp(): void
    {
        $this->percentageDiscountPriceCalculator = new PercentageDiscountPriceCalculator();
    }

    public function testImplementsActionBasedPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(ActionBasedPriceCalculatorInterface::class, $this->percentageDiscountPriceCalculator);
    }

    public function testSupportsOnlyPercentageDiscountCatalogPromotionAction(): void
    {
        $fixedDiscountAction = $this->createMock(CatalogPromotionActionInterface::class);
        $percentageDiscountAction = $this->createMock(CatalogPromotionActionInterface::class);

        $fixedDiscountAction
            ->expects($this->once())
            ->method('getType')
            ->willReturn(FixedDiscountPriceCalculator::TYPE)
        ;

        $percentageDiscountAction
            ->expects($this->once())
            ->method('getType')
            ->willReturn(PercentageDiscountPriceCalculator::TYPE)
        ;

        $this->assertFalse($this->percentageDiscountPriceCalculator->supports($fixedDiscountAction));
        $this->assertTrue($this->percentageDiscountPriceCalculator->supports($percentageDiscountAction));
    }

    public function testCalculatesPriceForGivenChannelPricingAndAction(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $action->expects($this->once())->method('getConfiguration')->willReturn(['amount' => 0.3]);

        $channelPricing->method('getPrice')->willReturn(1000);
        $channelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);

        $this->assertSame(700, $this->percentageDiscountPriceCalculator->calculate($channelPricing, $action));
    }

    public function testCalculatesAndRoundsPriceForGivenChannelPricingAndAction(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $action->expects($this->once())->method('getConfiguration')->willReturn(['amount' => 0.3]);

        $channelPricing->method('getPrice')->willReturn(951);
        $channelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);

        $this->assertSame(666, $this->percentageDiscountPriceCalculator->calculate($channelPricing, $action));
    }

    public function testCalculatesPriceForGivenChannelPricingAndActionWithTakingMinimumPriceIntoAccount(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $action->expects($this->once())->method('getConfiguration')->willReturn(['amount' => 0.7]);

        $channelPricing->method('getPrice')->willReturn(1000);
        $channelPricing->method('getMinimumPrice')->willReturn(500);

        $this->assertSame(500, $this->percentageDiscountPriceCalculator->calculate($channelPricing, $action));
    }
}
