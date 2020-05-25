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

class UpdatePage extends SymfonyPage implements UpdatePageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_account_address_book_update';
    }

    public function fillField(string $field, ?string $value): void
    {
        $field = $this->getElement(str_replace(' ', '_', strtolower($field)));
        $field->setValue($value);
    }

    public function getSpecifiedProvince(): string
    {
        $this->waitForElement(5, 'province_name');

        return $this->getElement('province_name')->getValue();
    }

    public function getSelectedProvince(): string
    {
        $this->waitForElement(5, 'province_code');

        return $this->getElement('selected_province')->getText();
    }

    public function specifyProvince(string $name): void
    {
        $this->waitForElement(5, 'province_name');

        $province = $this->getElement('province_name');
        $province->setValue($name);
    }

    public function selectProvince(string $name): void
    {
        $this->waitForElement(5, 'province_code');

        $province = $this->getElement('province_code');
        $province->selectOption($name);
    }

    public function selectCountry(string $name): void
    {
        JQueryHelper::waitForFormToStopLoading($this->getDocument());

        $country = $this->getElement('country');
        $country->selectOption($name);

        JQueryHelper::waitForFormToStopLoading($this->getDocument());
    }

    public function saveChanges(): void
    {
        JQueryHelper::waitForFormToStopLoading($this->getDocument());

        $this->getElement('save_button')->press();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'city' => '[data-test-city]',
            'country' => '[data-test-country]',
            'first_name' => '[data-test-first-name]',
            'last_name' => '[data-test-last-name]',
            'postcode' => '[data-test-postcode]',
            'province_name' => '[data-test-province-name]',
            'province_code' => '[data-test-province-code]',
            'save_button' => '[data-test-save-changes]',
            'selected_province' => '[data-test-province-code] option[selected="selected"]',
            'street' => '[data-test-street]',
        ]);
    }

    private function waitForElement(int $timeout, string $elementName): void
    {
        $this->getDocument()->waitFor($timeout, function () use ($elementName) {
            return $this->hasElement($elementName);
        });
    }
}
