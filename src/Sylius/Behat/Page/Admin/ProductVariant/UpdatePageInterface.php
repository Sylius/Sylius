<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @return bool
     */
    public function isCodeDisabled();

    /**
     * @param int $price
     */
    public function specifyPrice($price);

    public function disableTracking();

    public function enableTracking();

    /**
     * @return bool
     */
    public function isTracked();

    /**
     * @param ChannelInterface $channel
     * @param CurrencyInterface $currency
     *
     * @return string
     */
    public function getPricingConfigurationForChannelAndCurrencyCalculator(ChannelInterface $channel, CurrencyInterface $currency);

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getPriceForChannel($channelName);

    /**
     * @param string $channelName
     *
     * @return string
     */
    public function getOriginalPriceForChannel($channelName);

    /**
     * @param string $language
     *
     * @return string
     */
    public function getNameInLanguage($language);

    /**
     * @param int $amount
     */
    public function specifyCurrentStock($amount);

    /**
     * @return bool
     */
    public function isShippingRequired();
}
