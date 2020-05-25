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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Core\Model\AddressInterface;

class CreatePage extends SymfonyPage implements CreatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_account_address_book_create';
    }

    public function fillAddressData(AddressInterface $address): void
    {
        $this->getElement('first_name')->setValue($address->getFirstName());
        $this->getElement('last_name')->setValue($address->getLastName());
        $this->getElement('street')->setValue($address->getStreet());
        $this->getElement('country')->selectOption($address->getCountryCode());
        $this->getElement('city')->setValue($address->getCity());
        $this->getElement('postcode')->setValue($address->getPostcode());

        JQueryHelper::waitForFormToStopLoading($this->getDocument());
    }

    public function selectCountry(string $name): void
    {
        $this->getElement('country')->selectOption($name);

        JQueryHelper::waitForFormToStopLoading($this->getDocument());
    }

    public function addAddress(): void
    {
        $this->getElement('add_button')->press();
    }

    public function hasProvinceValidationMessage(): bool
    {
        return $this->hasElement('province_validation_message');
    }

    public function countValidationMessages(): int
    {
        return count($this->getDocument()->findAll('css', '[data-test-validation-error]'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_button' => '[data-test-add-address]',
            'city' => '[data-test-city]',
            'country' => '[data-test-country]',
            'first_name' => '[data-test-first-name]',
            'last_name' => '[data-test-last-name]',
            'postcode' => '[data-test-postcode]',
            'street' => '[data-test-street]',
            'province_validation_message' => '[data-test-validation-error]:contains("province")',
        ]);
    }
}
