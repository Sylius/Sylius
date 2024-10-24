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

final class PercentageDiscountPriceCalculator implements ActionBasedPriceCalculatorInterface
{
    public const TYPE = 'percentage_discount';

    public function supports(CatalogPromotionActionInterface $action): bool
    {
        return $action->getType() === self::TYPE;
    }

    public function calculate(ChannelPricingInterface $channelPricing, CatalogPromotionActionInterface $action): int
    {
        $price = (int) round($channelPricing->getPrice() - ($channelPricing->getPrice() * $action->getConfiguration()['amount']));

        $minimumPrice = $this->provideMinimumPrice($channelPricing);
        if ($price < $minimumPrice) {
            return $minimumPrice;
        }

        return $price;
    }

    private function provideMinimumPrice(ChannelPricingInterface $channelPricing): int
    {
        if ($channelPricing->getMinimumPrice() <= 0) {
            return 0;
        }

        return $channelPricing->getMinimumPrice();
    }
}
