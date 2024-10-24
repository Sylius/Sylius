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

namespace Sylius\Behat\Element\Admin\Product;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface ChannelPricingsFormElementInterface extends BaseFormElementInterface
{
    public function specifyPrice(ChannelInterface $channel, string $price): void;

    public function specifyOriginalPrice(ChannelInterface $channel, int $originalPrice): void;

    public function getPriceForChannel(ChannelInterface $channel): string;

    public function getOriginalPriceForChannel(ChannelInterface $channel): string;

    public function hasNoPriceForChannel(string $channelName): bool;
}
