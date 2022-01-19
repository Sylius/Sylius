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

namespace Sylius\Bundle\CoreBundle\DiscountApplicationCriteria;

use Sylius\Bundle\PromotionBundle\DiscountApplicationCriteria\DiscountApplicationCriteriaInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Webmozart\Assert\Assert;

final class ExclusiveCriteria implements DiscountApplicationCriteriaInterface
{
    public function isApplicable(CatalogPromotionInterface $catalogPromotion, array $context): bool
    {
        Assert::keyExists($context, 'channelPricing');

        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $context['channelPricing'];

        return !$channelPricing->hasExclusiveCatalogPromotionApplied();
    }
}
