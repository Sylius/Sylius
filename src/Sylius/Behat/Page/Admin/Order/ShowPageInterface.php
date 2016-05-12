<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ShowPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $customerName
     */
    public function hasCustomer($customerName);

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     */
    public function hasShippingAddress($customerName, $street, $postcode, $city, $countryName);

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     */
    public function hasBillingAddress($customerName, $street, $postcode, $city, $countryName);

    /**
     * @param string $shippingMethodName
     */
    public function hasShipment($shippingMethodName);

    /**
     * @param string $paymentMethodName
     */
    public function hasPayment($paymentMethodName);

    /**
     * @return int
     */
    public function countItems();

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isProductInTheList($productName);

    /**
     * @return string
     */
    public function getItemsTotal();

    /**
     * @return string
     */
    public function getTotal();

    /**
     * @return string
     */
    public function getShippingTotal();

    /**
     * @param string $shippingCharge
     *
     * @return bool
     */
    public function hasShippingCharge($shippingCharge);

    /**
     * @return string
     */
    public function getTaxTotal();

    /**
     * @return string
     */
    public function getPromotionTotal();

    /**
     * @param string $promotionDiscount
     *
     * @return bool
     */
    public function hasPromotionDiscount($promotionDiscount);

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemDiscount($itemName);
}
