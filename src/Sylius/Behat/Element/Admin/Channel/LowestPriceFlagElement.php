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

namespace Sylius\Behat\Element\Admin\Channel;

use FriendsOfBehat\PageObjectExtension\Element\Element;

final class LowestPriceFlagElement extends Element implements LowestPriceFlagElementInterface
{
    public function enable(): void
    {
        $this->getElement('lowest_price_for_discounted_products_visible')->check();
    }

    public function disable(): void
    {
        $this->getElement('lowest_price_for_discounted_products_visible')->uncheck();
    }

    public function isEnabled(): bool
    {
        return $this->getElement('lowest_price_for_discounted_products_visible')->isChecked();
    }

    protected function getDefinedElements(): array
    {
        return [
            'lowest_price_for_discounted_products_visible' => '#sylius_channel_channelPriceHistoryConfig_lowestPriceForDiscountedProductsVisible',
        ];
    }
}
