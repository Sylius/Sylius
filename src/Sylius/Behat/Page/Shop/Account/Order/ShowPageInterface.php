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

use Sylius\Behat\Page\SymfonyPageInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    /**
     * @return string
     */
    public function getNumber(): string;

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    public function hasShippingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool;

    /**
     * @param string $customerName
     * @param string $street
     * @param string $postcode
     * @param string $city
     * @param string $countryName
     *
     * @return bool
     */
    public function hasBillingAddress(string $customerName, string $street, string $postcode, string $city, string $countryName): bool;

    /**
     * @return string
     */
    public function getTotal(): string;

    /**
     * @return string
     */
    public function getSubtotal(): string;

    /**
     * @return int
     */
    public function countItems(): int;

    /**
     * @return string
     */
    public function getPaymentPrice(): string;

    /**
     * @param string $productName
     *
     * @return bool
     */
    public function isProductInTheList(string $productName): bool;

    /**
     * @return string
     */
    public function getItemPrice(): string;

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasShippingProvinceName(string $provinceName): bool;

    /**
     * @param string $provinceName
     *
     * @return bool
     */
    public function hasBillingProvinceName(string $provinceName): bool;
}
