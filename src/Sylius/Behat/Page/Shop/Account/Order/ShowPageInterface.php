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

namespace Sylius\Behat\Page\Shop\Account\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    /**
     * @return string
     */
    public function getNumber();

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    public function hasShippingAddress($customerName, $street, $postcode, $city, $countryName);

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    public function hasBillingAddress($customerName, $street, $postcode, $city, $countryName);

    public function getOrderShipmentStatus(): string;

    /**
     * @return string
     */
    public function getTotal();

    /**
     * @return string
     */
    public function getSubtotal();

    public function getShipmentStatus(): string;

    /**
     * @return int
     */
    public function countItems();

    /**
     * @return string
     */
    public function getPaymentPrice();

    public function getPaymentStatus(): string;

    public function getOrderPaymentStatus(): string;

    public function isProductInTheList(string $productName): bool;

    /**
     * @return string
     */
    public function getItemPrice();

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasShippingProvinceName($provinceName);

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasBillingProvinceName($provinceName);
}
