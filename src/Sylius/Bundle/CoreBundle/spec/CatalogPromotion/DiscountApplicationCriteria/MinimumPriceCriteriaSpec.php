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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\DiscountApplicationCriteria;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Webmozart\Assert\InvalidArgumentException;

final class MinimumPriceCriteriaSpec extends ObjectBehavior
{
    function it_implements_criteria_interface(): void
    {
        $this->shouldImplement(DiscountApplicationCriteriaInterface::class);
    }

    function it_returns_false_if_channel_price_is_already_minimum(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getPrice()->willReturn(300);
        $channelPricing->getMinimumPrice()->willReturn(300);

        $this->isApplicable(
            $catalogPromotion,
            ['action' => $action->getWrappedObject(), 'channelPricing' => $channelPricing->getWrappedObject()],
        )->shouldReturn(false);
    }

    function it_returns_true_if_channel_price_is_not_minimum(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
    ): void {
        $channelPricing->getPrice()->willReturn(300);
        $channelPricing->getMinimumPrice()->willReturn(0);

        $this->isApplicable(
            $catalogPromotion,
            ['action' => $action->getWrappedObject(), 'channelPricing' => $channelPricing->getWrappedObject()],
        )->shouldReturn(true);
    }

    function it_throws_exception_if_channel_pricing_is_not_provided(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
    ): void {
        $this->shouldThrow(InvalidArgumentException::class)->during('isApplicable', [$catalogPromotion, ['action' => $action->getWrappedObject()]]);
    }

    function it_throws_exception_if_channel_pricing_is_not_instance_of_channel_pricing(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
    ): void {
        $this->shouldThrow(InvalidArgumentException::class)->during(
            'isApplicable',
            [$catalogPromotion, ['action' => $action->getWrappedObject(), 'channelPricing' => 'string']],
        );
    }
}
