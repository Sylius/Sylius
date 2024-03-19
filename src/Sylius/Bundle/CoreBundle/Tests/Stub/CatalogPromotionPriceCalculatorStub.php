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

namespace Sylius\Bundle\CoreBundle\Tests\Stub;

use Sylius\Bundle\CoreBundle\Attribute\AsCatalogPromotionPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

#[AsCatalogPromotionPriceCalculator(priority: 9)]
final class CatalogPromotionPriceCalculatorStub implements CatalogPromotionPriceCalculatorInterface
{
    public function calculate(ChannelPricingInterface $channelPricing, CatalogPromotionActionInterface $action): int
    {
        return 0;
    }
}
