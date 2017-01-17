<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
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

    /**
     * @return string
     */
    public function getTotal();

    /**
     * @return string
     */
    public function getSubtotal();
    
    /**
     * @return int
     */
    public function countItems();

    /**
     * @return string
     */
    public function getPaymentPrice();

    /**
     * @param string $name
     *
     * @return bool
     */
    public function isProductInTheList($name);

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
