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

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Webmozart\Assert\Assert;

class IndexPage extends SymfonyPage implements IndexPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_account_address_book_index';
    }

    public function getAddressesCount(): int
    {
        $addressesCount = count($this->getElement('addresses')->findAll('css', '[data-test-address]'));

        if (!$this->hasNoDefaultAddress()) {
            ++$addressesCount;
        }

        return $addressesCount;
    }

    public function hasAddressOf(string $fullName): bool
    {
        return $this->hasElement('address', ['%full_name%' => $fullName]);
    }

    public function hasNoAddresses(): bool
    {
        return $this->hasElement('content', ['%message%' => 'You have no addresses defined']);
    }

    public function addressOfContains(string $fullName, string $value): bool
    {
        return $this->hasElement('address', ['%full_name%' => $fullName, '%value%' => $value]);
    }

    public function editAddress(string $fullName): void
    {
        $this->getElement('edit_address', ['%full_name%' => $fullName])->press();
    }

    public function deleteAddress(string $fullName): void
    {
        $this->getElement('delete_button', ['%full_name%' => $fullName])->press();
    }

    public function setAsDefault(string $fullName): void
    {
        $this->getElement('set_as_default_button', ['%full_name%' => $fullName])->press();
    }

    public function hasNoDefaultAddress(): bool
    {
        return !$this->hasElement('default_address');
    }

    public function getFullNameOfDefaultAddress(): string
    {
        $fullNameElement = $this->getElement('default_address');

        Assert::notNull($fullNameElement, 'There should be a default address\'s full name.');

        return $fullNameElement->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'address' => '[data-test-address-context="%full_name%"]',
            'address_contains' => '[data-test-address-context="%full_name%"]:contains("%value%")',
            'addresses' => '[data-test-addresses]',
            'content' => '[data-test-flash-message="info"]:contains("%message%")',
            'default_address' => '[data-test-default-address] [data-test-full-name]',
            'delete_button' => '[data-test-address="%full_name%"] [data-test-delete-button]',
            'edit_address' => '[data-test-address="%full_name%"] [data-test-edit-button] [data-test-button]',
            'set_as_default_button' => '[data-test-address="%full_name%"] [data-test-set-as-default-button]',
        ]);
    }
}
