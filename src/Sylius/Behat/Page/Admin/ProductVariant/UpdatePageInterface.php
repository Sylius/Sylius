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
    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @param int $price
     */
    public function specifyPrice(int $price): void;

    public function disableTracking(): void;

    public function enableTracking(): void;

    /**
     * @return bool
     */
    public function isTracked(): bool;

    /**
     * @param CurrencyInterface $currency
     *
     * @return string
     */
    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency): string;

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getPriceForChannel(string $channelName): string;

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getOriginalPriceForChannel(string $channelName): string;

    /**
     * @param string $language
     *
     * @return string
     */
    public function getNameInLanguage(string $language): string;

    /**
     * @param int $amount
     */
    public function specifyCurrentStock(int $amount): void;

    /**
     * @return bool
     */
    public function isShippingRequired(): bool;
}
