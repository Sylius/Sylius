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

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function isCodeDisabled(): bool;

    public function specifyPrice(int $price): void;

    public function disableTracking(): void;

    public function enableTracking(): void;

    public function isTracked(): bool;

    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string;

    public function getPriceForChannel(string $channelName): string;

    public function getOriginalPriceForChannel(string $channelName): string;

    public function getNameInLanguage(string $language): string;

    public function specifyCurrentStock(int $amount): void;

    public function isShippingRequired(): bool;
}
