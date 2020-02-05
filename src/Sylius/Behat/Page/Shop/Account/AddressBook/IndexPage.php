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

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use Behat\Mink\Element\NodeElement;
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
        $addressesCount = count($this->getElement('addresses')->findAll('css', 'address'));

        if (!$this->hasNoDefaultAddress()) {
            ++$addressesCount;
        }

        return $addressesCount;
    }

    public function hasAddressOf(string $fullName): bool
    {
        return null !== $this->getAddressOf($fullName);
    }

    public function hasNoAddresses(): bool
    {
        return $this->getDocument()->hasContent('You have no addresses defined');
    }

    public function addressOfContains(string $fullName, string $value): bool
    {
        $address = $this->getAddressOf($fullName);

        return $address->has('css', sprintf('address:contains("%s")', $value));
    }

    public function editAddress(string $fullName): void
    {
        $addressToEdit = $this->getAddressOf($fullName);
        $addressToEdit->findLink('Edit')->press();
    }

    public function deleteAddress(string $fullName): void
    {
        $addressToDelete = $this->getAddressOf($fullName);
        $addressToDelete->pressButton('Delete');
    }

    public function setAsDefault(string $fullName): void
    {
        $addressToSetAsDefault = $this->getAddressOf($fullName);
        $addressToSetAsDefault->pressButton('Set as default');
    }

    public function hasNoDefaultAddress(): bool
    {
        return !$this->hasElement('default_address');
    }

    public function getFullNameOfDefaultAddress(): string
    {
        $fullNameElement = $this->getElement('default_address')->find('css', 'address > strong');

        Assert::notNull($fullNameElement, 'There should be a default address\'s full name.');

        return $fullNameElement->getText();
    }

    private function getAddressOf(string $fullName): ?NodeElement
    {
        return $this->getElement('addresses')->find('css', sprintf('div.address:contains("%s")', $fullName));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'addresses' => '#sylius-addresses',
            'default_address' => '#sylius-default-address',
        ]);
    }
}
