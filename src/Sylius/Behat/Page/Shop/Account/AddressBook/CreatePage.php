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
        return null !== $this->getDocument()->find('css', '.sylius-validation-error:contains("province")');
    }

    public function countValidationMessages(): int
    {
        return count($this->getDocument()->findAll('css', '.sylius-validation-error'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'add_button' => 'button:contains("Add")',
            'city' => '#sylius_address_city',
            'country' => '#sylius_address_countryCode',
            'first_name' => '#sylius_address_firstName',
            'last_name' => '#sylius_address_lastName',
            'postcode' => '#sylius_address_postcode',
            'street' => '#sylius_address_street',
        ]);
    }
}
