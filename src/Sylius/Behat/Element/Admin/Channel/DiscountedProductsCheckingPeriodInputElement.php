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

final class DiscountedProductsCheckingPeriodInputElement extends Element implements DiscountedProductsCheckingPeriodInputElementInterface
{
    public function specifyPeriod(int $period): void
    {
        $this->getElement('discounted_products_checking_period')->setValue($period);
    }

    public function getPeriod(): int
    {
        return (int) $this->getElement('discounted_products_checking_period')->getValue();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'discounted_products_checking_period' => '#sylius_channel_channelPriceHistoryConfig_lowestPriceForDiscountedProductsCheckingPeriod',
        ]);
    }
}
