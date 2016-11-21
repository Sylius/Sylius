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
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param int $price
     */
    public function specifyPrice($price);

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
     */
    public function nameIt($name);

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
}
