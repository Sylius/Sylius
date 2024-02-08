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

namespace Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

interface ActionBasedPriceCalculatorInterface
{
    public function supports(CatalogPromotionActionInterface $action): bool;

    public function calculate(ChannelPricingInterface $channelPricing, CatalogPromotionActionInterface $action): int;
}
