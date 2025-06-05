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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\ExclusiveCriteria;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Webmozart\Assert\InvalidArgumentException;

final class ExclusiveCriteriaTest extends TestCase
{
    private ExclusiveCriteria $exclusiveCriteria;

    protected function setUp(): void
    {
        $this->exclusiveCriteria = new ExclusiveCriteria();
    }

    public function testImplementsCriteriaInterface(): void
    {
        $this->assertInstanceOf(DiscountApplicationCriteriaInterface::class, $this->exclusiveCriteria);
    }

    public function testReturnsFalseIfChannelPricingExclusivePromotionIsApplied(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $channelPricing
            ->expects($this->once())
            ->method('hasExclusiveCatalogPromotionApplied')
            ->willReturn(true)
        ;

        $this->assertFalse($this->exclusiveCriteria->isApplicable(
            $catalogPromotion,
            ['action' => $action, 'channelPricing' => $channelPricing],
        ));
    }

    public function testReturnsTrueIfChannelPricingExclusivePromotionIsNotApplied(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $channelPricing
            ->expects($this->once())
            ->method('hasExclusiveCatalogPromotionApplied')
            ->willReturn(false)
        ;

        $this->assertTrue($this->exclusiveCriteria->isApplicable(
            $catalogPromotion,
            ['action' => $action, 'channelPricing' => $channelPricing],
        ));
    }

    public function testThrowsExceptionIfChannelPricingIsNotProvided(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->exclusiveCriteria->isApplicable($catalogPromotion, ['action' => $action]);
    }

    public function testThrowsExceptionIfChannelPricingIsNotInstanceOfChannelPricing(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->exclusiveCriteria
            ->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => 'string'])
        ;
    }
}
