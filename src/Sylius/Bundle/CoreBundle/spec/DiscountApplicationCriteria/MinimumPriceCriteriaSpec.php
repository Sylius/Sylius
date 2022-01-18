<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\DiscountApplicationCriteria;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class MinimumPriceCriteriaSpec extends ObjectBehavior
{
    function it_implements_criteria_interface(): void
    {
        $this->shouldImplement(DiscountApplicationCriteriaInterface::class);
    }

    function it_returns_false_if_channel_price_is_already_minimum(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing
    ): void {
        $channelPricing->getPrice()->willReturn(300);
        $channelPricing->getMinimumPrice()->willReturn(300);

        $this->isApplicable($catalogPromotion, $action, $channelPricing)->shouldReturn(false);
    }

    function it_returns_true_if_channel_price_is_not_minimum(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing
    ): void {
        $channelPricing->getPrice()->willReturn(300);
        $channelPricing->getMinimumPrice()->willReturn(0);

        $this->isApplicable($catalogPromotion, $action, $channelPricing)->shouldReturn(true);
    }
}
