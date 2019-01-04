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

namespace Sylius\Behat\Page\Shop\Product;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\PageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

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
     * @param string $optionValue
     *
     * @throws ElementNotFoundException
     */
    public function addToCartWithOption(ProductOptionInterface $option, $optionValue);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCurrentVariantName();

    /**
     * @param string $url
     */
    public function visit($url);

    public function getAttributeByName(string $name): ?string;

    /**
     * @return array
     */
    public function getAttributes();

    /**
     * @return bool
     */
    public function hasProductOutOfStockValidationMessage(ProductInterface $product);

    /**
     * @param int $timeout
     */
    public function waitForValidationErrors($timeout);

    /**
     * @return bool
     */
    public function isOutOfStock();

    /**
     * @return bool
     */
    public function hasAddToCartButton();

    /**
     * @return string
     */
    public function getPrice();

    /**
     * @return int
     */
    public function countReviews();

    /**
     * @return bool
     */
    public function isMainImageDisplayed();

    /**
     * @param string $title
     *
     * @return bool
     */
    public function hasReviewTitled($title);

    /**
     * @return float
     */
    public function getAverageRating();

    /**
     * @param string $optionName
     * @param string $optionValue
     */
    public function selectOption($optionName, $optionValue);

    /**
     * @param string $variantName
     */
    public function selectVariant($variantName);

    /**
     * @param string $productAssociationName
     *
     * @return bool
     */
    public function hasAssociation($productAssociationName);

    /**
     * @param string $productName
     * @param string $productAssociationName
     *
     * @return bool
     */
    public function hasProductInAssociation($productName, $productAssociationName);
}
