<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;
use Sylius\Component\Product\Model\OptionInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface ShowPageInterface extends PageInterface
{
    /**
     * @throws ElementNotFoundException
     */
    public function addToCart();

    /**
     * @param string $quantity
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithQuantity($quantity);

    /**
     * @param string $variant
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithVariant($variant);

    /**
     * @param OptionInterface $option
     * @param string $optionValue
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithOption(OptionInterface $option, $optionValue);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $url
     */
    public function visit($url);

    /**
     * @param string $attributeName
     * @param string $AttributeValue
     *
     * @return bool
     */
     public function isAttributeWithValueOnPage($attributeName, $AttributeValue);
}
