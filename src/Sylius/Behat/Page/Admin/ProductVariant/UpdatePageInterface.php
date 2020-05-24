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
    public function disableTracking(): void;

    public function enableTracking(): void;

    public function isCodeDisabled(): bool;

    public function isSelectedOptionValueOnPage(string $optionName, string $valueName): bool;

    public function isShippingRequired(): bool;

    public function isTracked(): bool;

    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string;

    public function getPriceForChannel(string $channelName): string;

    public function getOriginalPriceForChannel(string $channelName): string;

    public function getNameInLanguage(string $language): string;

    public function selectOption(string $optionName, string $optionValue): void;

    public function isShowInShopButtonDisabled(): bool;

    public function showProductInChannel(string $channel): void;

    public function showProductInSingleChannel(): void;

    public function specifyCurrentStock(int $amount): void;

    public function specifyPrice(int $price): void;

    public function disable(): void;

    public function isEnabled(): bool;

    public function enable(): void;
}
