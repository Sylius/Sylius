<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Account\Order;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;

interface ShowPageInterface extends SymfonyPageInterface
{
    public function getNumber(): string;

    public function hasShippingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): bool;

    public function hasBillingAddress(
        string $customerName,
        string $street,
        string $postcode,
        string $city,
        string $countryName,
    ): bool;

    public function choosePaymentMethod(PaymentMethodInterface $paymentMethod): void;

    public function pay(): void;

    public function getChosenPaymentMethod(): string;

    public function getTotal(): string;

    public function getSubtotal(): string;

    public function getOrderShipmentStatus(): string;

    public function getShipmentStatus(): string;

    public function countItems(): int;

    public function getPaymentPrice(): string;

    public function getPaymentStatus(): string;

    public function getOrderPaymentStatus(): string;

    public function isProductInTheList(string $productName): bool;

    public function getItemPrice(): string;

    public function hasShippingProvinceName(string $provinceName): bool;

    public function hasBillingProvinceName(string $provinceName): bool;
}
