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

final class FixedDiscountPriceCalculatorTest extends TestCase
{
    private FixedDiscountPriceCalculator $fixedDiscountPriceCalculator;

    protected function setUp(): void
    {
        $this->fixedDiscountPriceCalculator = new FixedDiscountPriceCalculator();
    }

    public function testImplementsActionBasedPriceCalculatorInterface(): void
    {
        $this->assertInstanceOf(ActionBasedPriceCalculatorInterface::class, $this->fixedDiscountPriceCalculator);
    }

    public function testSupportsOnlyFixedDiscountCatalogPromotionAction(): void
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

        $this->assertTrue($this->fixedDiscountPriceCalculator->supports($fixedDiscountAction));
        $this->assertFalse($this->fixedDiscountPriceCalculator->supports($percentageDiscountAction));
    }

    public function testCalculatesPriceForGivenChannelPricingAndAction(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $action->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn(['WEB' => ['amount' => 200]])
        ;

        $channelPricing->method('getChannelCode')->willReturn('WEB');
        $channelPricing->expects($this->once())->method('getPrice')->willReturn(1000);
        $channelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);

        $this->assertSame(800, $this->fixedDiscountPriceCalculator->calculate($channelPricing, $action));
    }

    public function testCalculatesPriceForGivenChannelPricingAndActionWithTakingMinimumPriceIntoAccount(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $action->method('getConfiguration')->willReturn(['WEB' => ['amount' => 600]]);

        $channelPricing->method('getChannelCode')->willReturn('WEB');
        $channelPricing->method('getPrice')->willReturn(1000);
        $channelPricing->method('getMinimumPrice')->willReturn(500);

        $this->assertSame(500, $this->fixedDiscountPriceCalculator->calculate($channelPricing, $action));
    }

    public function testCalculatesPriceForGivenChannelPricingAndActionWithoutMinimumPriceSpecified(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $action->method('getConfiguration')->willReturn(['WEB' => ['amount' => 1100]]);
        $channelPricing->method('getChannelCode')->willReturn('WEB');
        $channelPricing->expects($this->once())->method('getPrice')->willReturn(1000);
        $channelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);
        $this->assertSame(0, $this->fixedDiscountPriceCalculator->calculate($channelPricing, $action));
    }
}
