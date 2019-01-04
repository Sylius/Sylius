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

use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface IndexPageInterface extends PageInterface
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
     *
     * @return string
     */
    public function getProductPrice($productName);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isProductOnPageWithName($name);

    /**
     * @return bool
     */
    public function hasProductsInOrder(array $productNames);
}
