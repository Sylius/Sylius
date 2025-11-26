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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Exception\ActionBasedPriceCalculatorNotFoundException;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class ActionBasedDiscountApplicatorTest extends TestCase
{
    private CatalogPromotionPriceCalculatorInterface&MockObject $priceCalculator;

    private DiscountApplicationCriteriaInterface&MockObject $minimumPriceCriteria;

    private DiscountApplicationCriteriaInterface&MockObject $exclusiveCriteria;

    private ActionBasedDiscountApplicator $actionBasedDiscountApplicator;

    protected function setUp(): void
    {
        $this->priceCalculator = $this->createMock(CatalogPromotionPriceCalculatorInterface::class);
        $this->minimumPriceCriteria = $this->createMock(DiscountApplicationCriteriaInterface::class);
        $this->exclusiveCriteria = $this->createMock(DiscountApplicationCriteriaInterface::class);
        $this->actionBasedDiscountApplicator = new ActionBasedDiscountApplicator(
            $this->priceCalculator,
            [$this->minimumPriceCriteria, $this->exclusiveCriteria],
        );
    }

    public function testImplementsActionBasedDiscountApplicatorInterface(): void
    {
        $this->assertInstanceOf(ActionBasedDiscountApplicatorInterface::class, $this->actionBasedDiscountApplicator);
    }

    public function testAppliesDiscountIfAllCriteriaAreValid(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $channelPricing->method('getAppliedPromotions');
        $channelPricing->method('getOriginalPrice')->willReturn(300);

        $this->priceCalculator->method('calculate')->with($channelPricing, $action)->willReturn(100);

        $channelPricing->expects($this->once())->method('setPrice')->with(100);
        $channelPricing->expects($this->once())->method('addAppliedPromotion')->with($catalogPromotion);

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    public function testDoesNotApplyDiscountIfAtLeastOneCriteriaIsInvalid(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(false)
        ;

        $this->priceCalculator->expects($this->never())->method('calculate');

        $channelPricing->expects($this->never())->method('setPrice');
        $channelPricing->expects($this->never())->method('addAppliedPromotion');

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing(
            $catalogPromotion,
            $action,
            $channelPricing,
        );
    }

    public function testDoesNotSetOriginalPriceDuringApplicationIfItsAlreadyThere(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $channelPricing->expects($this->once())->method('getAppliedPromotions');
        $channelPricing->expects($this->once())->method('getOriginalPrice')->willReturn(200);
        $channelPricing->expects($this->never())->method('getPrice');
        $channelPricing->expects($this->never())->method('setOriginalPrice');

        $this->priceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($channelPricing, $action)
            ->willReturn(100)
        ;

        $channelPricing->expects($this->once())->method('setPrice')->with(100);
        $channelPricing->expects($this->once())->method('addAppliedPromotion')->with($catalogPromotion);

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    public function testSetsOriginalPriceOnChannelPricingIfOriginalPriceIsNotSet(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $channelPricing->expects($this->once())->method('getAppliedPromotions');
        $channelPricing->expects($this->once())->method('getOriginalPrice')->willReturn(null);
        $channelPricing->expects($this->once())->method('getPrice')->willReturn(200);
        $channelPricing->expects($this->once())->method('setOriginalPrice')->with(200);

        $this->priceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($channelPricing, $action)
            ->willReturn(100)
        ;

        $channelPricing->expects($this->once())->method('setPrice')->with(100);
        $channelPricing->expects($this->once())->method('addAppliedPromotion')->with($catalogPromotion);

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    public function testDoesNotApplyDiscountIfPriceCalculatorThrowsException(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $channelPricing->expects($this->never())->method('getOriginalPrice');
        $channelPricing->expects($this->never())->method('setPrice');
        $channelPricing->expects($this->never())->method('addAppliedPromotion');

        $this->priceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($channelPricing, $action)
            ->willThrowException(new ActionBasedPriceCalculatorNotFoundException())
        ;

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    public function testSetsPriceAsOriginalPriceWhenThereAreNoAppliedPromotionsAndOriginalPriceIsSpecified(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $channelPricing->expects($this->once())
            ->method('getAppliedPromotions')
            ->willReturn(new ArrayCollection())
        ;

        $channelPricing->method('getOriginalPrice')->willReturn(300);

        $this->priceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($channelPricing, $action)
            ->willReturn(100)
        ;

        $expectedPrices = [300, 100];
        $callCount = 0;

        $channelPricing->expects($this->exactly(2))
            ->method('setPrice')
            ->willReturnCallback(function ($price) use (&$expectedPrices, &$callCount) {
                $this->assertSame($expectedPrices[$callCount], $price);
                ++$callCount;
            })
        ;

        $channelPricing->expects($this->once())
            ->method('addAppliedPromotion')
            ->with($catalogPromotion)
        ;

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    public function testDoesNotSetPriceAsOriginalPriceWhenThereAreAppliedPromotionsAndOriginalPriceIsSpecified(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->minimumPriceCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $this->exclusiveCriteria
            ->expects($this->once())
            ->method('isApplicable')
            ->with($catalogPromotion, ['action' => $action, 'channelPricing' => $channelPricing])
            ->willReturn(true)
        ;

        $channelPricing
            ->expects($this->once())
            ->method('getAppliedPromotions')
            ->willReturn(new ArrayCollection([$catalogPromotion]))
        ;

        $channelPricing->expects($this->once())->method('getOriginalPrice')->willReturn(300);

        $this->priceCalculator
            ->expects($this->once())
            ->method('calculate')
            ->with($channelPricing, $action)
            ->willReturn(100)
        ;

        $expectedPrices = [100];
        $callCount = 0;

        $channelPricing->expects($this->once())
            ->method('setPrice')
            ->willReturnCallback(function ($price) use (&$expectedPrices, &$callCount) {
                $this->assertSame($expectedPrices[$callCount], $price);
                ++$callCount;
            })
        ;

        $channelPricing->expects($this->once())
            ->method('addAppliedPromotion')
            ->with($catalogPromotion)
        ;

        $this->actionBasedDiscountApplicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }

    public function testItDoesNotSetPriceAsOriginalPriceWhenThereAreNoAppliedPromotionsAndOriginalPriceIsNotSpecified(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $appliedPromotions = $this->createMock(Collection::class);

        $appliedPromotions->method('isEmpty')->willReturn(true);
        $channelPricing->method('getAppliedPromotions')->willReturn($appliedPromotions);
        $channelPricing->method('getOriginalPrice')->willReturn(null);
        $channelPricing->method('getPrice')->willReturn(2000);
        $channelPricing->expects($this->once())->method('setOriginalPrice')->with(2000);
        $channelPricing->expects($this->once())->method('setPrice')->with(1000);

        $priceCalculator = $this->createMock(CatalogPromotionPriceCalculatorInterface::class);
        $priceCalculator->method('calculate')->willReturn(1000);

        $criterion = $this->createMock(DiscountApplicationCriteriaInterface::class);
        $criterion->method('isApplicable')->willReturn(true);

        $discountApplicatorCriteria = [$criterion];

        $applicator = new ActionBasedDiscountApplicator($priceCalculator, $discountApplicatorCriteria);

        $applicator->applyDiscountOnChannelPricing($catalogPromotion, $action, $channelPricing);
    }
}
