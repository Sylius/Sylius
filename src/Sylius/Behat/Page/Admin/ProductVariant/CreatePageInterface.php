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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param int $price
     * @param string $channelName
     */
    public function specifyPrice($price, $channelName);

    /**
     * @param int $originalPrice
     * @param string $channelName
     */
    public function specifyOriginalPrice($originalPrice, $channelName);

    /**
     * @param int $height
     * @param int $width
     * @param int $depth
     * @param int $weight
     */
    public function specifyHeightWidthDepthAndWeight($height, $width, $depth, $weight);

    /**
     * @param string $code
     */
    public function specifyCode($code);

    /**
     * @param int $currentStock
     */
    public function specifyCurrentStock($currentStock);

    /**
     * @param string $name
     * @param string $language
     */
    public function nameItIn($name, $language);

    /**
     * @param string $optionName
     * @param string $optionValue
     */
    public function selectOption($optionName, $optionValue);

    /**
     * @param string $name
     */
    public function choosePricingCalculator($name);

    /**
     * @param int $price
     * @param ChannelInterface $channel
     * @param CurrencyInterface $currency
     */
    public function specifyPriceForChannelAndCurrency($price, ChannelInterface $channel, CurrencyInterface $currency);

    /**
     * @return string
     */
    public function getValidationMessageForForm();

    /**
     * @param string $shippingCategoryName
     */
    public function selectShippingCategory($shippingCategoryName);

    /**
     * @return string
     */
    public function getPricesValidationMessage();

    /**
     * @param bool $isShippingRequired
     */
    public function setShippingRequired($isShippingRequired);
}
