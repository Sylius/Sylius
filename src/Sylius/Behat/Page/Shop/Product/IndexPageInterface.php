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

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface IndexPageInterface
{
    /**
     * @return int
     */
    public function countProductsItems();

    /**
     * @return string
     */
    public function getFirstProductNameFromList();

    /**
     * @return string
     */
    public function getLastProductNameFromList();

    /**
     * @param string $name
     */
    public function search($name);

    /**
     * @param string $order
     */
    public function sort($order);

    public function clearFilter();

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isProductOnList($productName);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @param string $productName
     * @param string $productPrice
     *
     * @return bool
     */
    public function isProductWithPriceOnList($productName, $productPrice);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isProductOnPageWithName($name);

    /**
     * @param array $productNames
     *
     * @return bool
     */
    public function hasProductsInOrder(array $productNames);
}
