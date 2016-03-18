<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Cart;

use Behat\Mink\Exception\ElementNotFoundException as BehatElementNotFoundException;
use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CartSummaryPageInterface extends PageInterface
{
    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getGrandTotal();

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getTaxTotal();

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getShippingTotal();

    /**
     * @return string
     *
     * @throws ElementNotFoundException
     */
    public function getPromotionTotal();

    /**
     * @param string $productName
     *
     * @throws ElementNotFoundException
     */
    public function getItemRegularPrice($productName);

    /**
     * @param string $productName
     *
     * @throws ElementNotFoundException
     */
    public function getItemDiscountPrice($productName);

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isItemDiscounted($productName);

    /**
     * @param string $productName
     */
    public function removeProduct($productName);

    /**
     * @param string $productName
     * @param int $quantity
     *
     * @throws BehatElementNotFoundException
     */
    public function changeQuantity($productName, $quantity);
}
