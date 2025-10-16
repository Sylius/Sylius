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
use Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria\MinimumPriceCriteria;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Webmozart\Assert\InvalidArgumentException;

final class MinimumPriceCriteriaTest extends TestCase
{
    private MinimumPriceCriteria $minimumPriceCriteria;

    protected function setUp(): void
    {
        $this->minimumPriceCriteria = new MinimumPriceCriteria();
    }

    public function testImplementsCriteriaInterface(): void
    {
        $this->assertInstanceOf(DiscountApplicationCriteriaInterface::class, $this->minimumPriceCriteria);
    }

    public function testReturnsFalseIfChannelPriceIsAlreadyMinimum(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $channelPricing->expects($this->once())->method('getPrice')->willReturn(300);
        $channelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(300);

        $this->assertFalse($this->minimumPriceCriteria->isApplicable(
            $catalogPromotion,
            ['action' => $action, 'channelPricing' => $channelPricing],
        ));
    }

    public function testReturnsTrueIfChannelPriceIsNotMinimum(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $channelPricing->expects($this->once())->method('getPrice')->willReturn(300);
        $channelPricing->expects($this->once())->method('getMinimumPrice')->willReturn(0);

        $this->assertTrue($this->minimumPriceCriteria->isApplicable(
            $catalogPromotion,
            ['action' => $action, 'channelPricing' => $channelPricing],
        ));
    }

    public function testThrowsExceptionIfChannelPricingIsNotProvided(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->minimumPriceCriteria->isApplicable($catalogPromotion, ['action' => $action]);
    }

    public function testThrowsExceptionIfChannelPricingIsNotInstanceOfChannelPricing(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);
        $action = $this->createMock(CatalogPromotionActionInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->minimumPriceCriteria
            ->isApplicable($catalogPromotion, ['action' => $action, 'channelPricing' => 'string']);
    }
}
