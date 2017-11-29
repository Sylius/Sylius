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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param int $price
     * @param string $channelName
     */
    public function specifyPrice(int $price, string $channelName): void;

    /**
     * @param int $originalPrice
     * @param string $channelName
     */
    public function specifyOriginalPrice(int $originalPrice, string $channelName): void;

    /**
     * @param int $height
     * @param int $width
     * @param int $depth
     * @param int $weight
     */
    public function specifyHeightWidthDepthAndWeight(int $height, int $width, int $depth, int $weight): void;

    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param int $currentStock
     */
    public function specifyCurrentStock(int $currentStock): void;

    /**
     * @param string $name
     * @param string $language
     */
    public function nameItIn(string $name, string $language): void;

    /**
     * @param string $optionName
     * @param string $optionValue
     */
    public function selectOption(string $optionName, string $optionValue): void;

    /**
     * @param string $name
     */
    public function choosePricingCalculator(string $name): void;

    /**
     * @param int $price
     * @param CurrencyInterface $currency
     */
    public function specifyPriceForChannelAndCurrency(int $price, ChannelInterface $channel, CurrencyInterface $currency): void;

    /**
     * @return string
     */
    public function getValidationMessageForForm(): string;

    /**
     * @param string $shippingCategoryName
     */
    public function selectShippingCategory(string $shippingCategoryName): void;

    /**
     * @return string
     */
    public function getPricesValidationMessage(): string;

    /**
     * @param bool $isShippingRequired
     */
    public function setShippingRequired(bool $isShippingRequired): void;
}
