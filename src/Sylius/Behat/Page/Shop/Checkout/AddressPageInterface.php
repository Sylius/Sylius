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

namespace Sylius\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Core\Model\AddressInterface;

interface AddressPageInterface extends SymfonyPageInterface
{
    /**
     * @throws \RuntimeException
     */
    public function chooseDifferentBillingAddress(): void;

    /**
     * @return bool
     */
    public function checkInvalidCredentialsValidation(): bool;

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    /**
     * @param AddressInterface $billingAddress
     */
    public function specifyBillingAddress(AddressInterface $billingAddress): void;

    /**
     * @param string $province
     */
    public function selectBillingAddressProvince(string $province): void;

    /**
     * @param AddressInterface $shippingAddress
     */
    public function specifyShippingAddress(AddressInterface $shippingAddress): void;

    /**
     * @param string $province
     */
    public function selectShippingAddressProvince(string $province): void;

    /**
     * @return bool
     */
    public function canSignIn(): bool;

    public function signIn(): void;

    /**
     * @param string $email
     */
    public function specifyEmail(string $email): void;

    /**
     * @param string $fullName
     */
    public function specifyShippingAddressFullName(string $fullName): void;

    /**
     * @param string $password
     */
    public function specifyPassword(string $password): void;

    /**
     * @param string $itemName
     *
     * @return string
     */
    public function getItemSubtotal(string $itemName): string;

    /**
     * @return string
     */
    public function getShippingAddressCountry(): string;

    public function nextStep(): void;

    public function backToStore(): void;

    /**
     * @param string $provinceName
     */
    public function specifyBillingAddressProvince(string $provinceName): void;

    /**
     * @param string $provinceName
     */
    public function specifyShippingAddressProvince(string $provinceName): void;

    /**
     * @return bool
     */
    public function hasShippingAddressInput(): bool;

    /**
     * @return bool
     */
    public function hasBillingAddressInput(): bool;

    /**
     * @param AddressInterface $address
     */
    public function selectShippingAddressFromAddressBook(AddressInterface $address): void;

    /**
     * @param AddressInterface $address
     */
    public function selectBillingAddressFromAddressBook(AddressInterface $address): void;

    /**
     * @return AddressInterface
     */
    public function getPreFilledShippingAddress(): AddressInterface;

    /**
     * @return AddressInterface
     */
    public function getPreFilledBillingAddress(): AddressInterface;
}
