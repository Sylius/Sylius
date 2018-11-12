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
    public function chooseDifferentBillingAddress();

    public function checkInvalidCredentialsValidation(): bool;

    public function checkValidationMessageFor(string $element, string $message): bool;

    public function specifyBillingAddress(AddressInterface $billingAddress);

    public function selectBillingAddressProvince(string $province);

    public function specifyShippingAddress(AddressInterface $shippingAddress);

    public function selectShippingAddressProvince(string $province);

    public function canSignIn(): bool;

    public function signIn();

    public function specifyEmail(string $email);

    public function specifyShippingAddressFullName(string $fullName);

    public function specifyPassword(string $password);

    public function getItemSubtotal(string $itemName): string;

    public function getShippingAddressCountry(): string;

    public function nextStep();

    public function backToStore();

    public function specifyBillingAddressProvince(string $provinceName);

    public function specifyShippingAddressProvince(string $provinceName);

    public function hasShippingAddressInput(): bool;

    public function hasBillingAddressInput(): bool;

    public function selectShippingAddressFromAddressBook(AddressInterface $address);

    public function selectBillingAddressFromAddressBook(AddressInterface $address);

    public function getPreFilledShippingAddress(): AddressInterface;

    public function getPreFilledBillingAddress(): AddressInterface;
}
