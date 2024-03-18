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

namespace Sylius\Behat\Page\Admin\ChannelPricingLogEntry;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Webmozart\Assert\Assert;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function isLogEntryWithPriceAndOriginalPrice(string $price, string $originalPrice): bool
    {
        $availablePrices = $this->getColumnFields('price');
        $availableOriginalPrices = $this->getColumnFields('originalPrice');
        $dates = $this->getColumnFields('loggedAt');

        foreach ($availablePrices as $key => $value) {
            Assert::notEmpty($dates[$key]);

            if (
                $availablePrices[$key] === $price &&
                $availableOriginalPrices[$key] === $originalPrice
            ) {
                return true;
            }
        }

        return false;
    }

    public function isLogEntryWithPriceAndOriginalPriceOnPosition(string $price, string $originalPrice, int $position): bool
    {
        $availablePrices = $this->getColumnFields('price');
        $availableOriginalPrices = $this->getColumnFields('originalPrice');
        $dates = $this->getColumnFields('loggedAt');
        Assert::notEmpty($dates[$position - 1]);

        return $availablePrices[$position - 1] === $price && $availableOriginalPrices[$position - 1] === $originalPrice;
    }
}
