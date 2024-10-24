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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator;

use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

interface ActionBasedDiscountApplicatorInterface
{
    public function applyDiscountOnChannelPricing(
        CatalogPromotionInterface $catalogPromotion,
        CatalogPromotionActionInterface $action,
        ChannelPricingInterface $channelPricing,
    ): void;
}
