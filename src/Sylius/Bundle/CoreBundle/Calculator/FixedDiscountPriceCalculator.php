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

namespace Sylius\Bundle\CoreBundle\Calculator;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;

final class FixedDiscountPriceCalculator implements ActionBasedPriceCalculatorInterface
{
    public function supports(CatalogPromotionActionInterface $action): bool
    {
        return $action->getType() === CatalogPromotionActionInterface::TYPE_FIXED_DISCOUNT;
    }

    public function calculate(ChannelPricingInterface $channelPricing, CatalogPromotionActionInterface $action): int
    {
        if (!isset($action->getConfiguration()[$channelPricing->getChannelCode()])) {
            return $channelPricing->getPrice();
        }

        $price = $channelPricing->getPrice() - $action->getConfiguration()[$channelPricing->getChannelCode()]['amount'];

        if ($price < $channelPricing->getMinimumPrice()) {
            return $channelPricing->getMinimumPrice();
        }

        return $price;
    }
}
