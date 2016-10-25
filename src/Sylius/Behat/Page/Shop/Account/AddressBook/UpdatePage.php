<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account\AddressBook;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class UpdatePage extends SymfonyPage implements UpdatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function fillField($field, $value)
    {
        $field = $this->getElement(str_replace(' ', '_',strtolower($field)));
        $field->setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyProvince($name)
    {
        $this->waitForElement(5, 'province');

        $province = $this->getElement('province');
        $province->setValue($name);
    }

    /**
     * {@inheritdoc}
     */
    public function selectProvince($name)
    {
        $this->waitForElement(5, 'province');

        $province = $this->getElement('province');
        $province->selectOption($name);
    }

    /**
     * {@inheritdoc}
     */
    public function selectCountry($name)
    {
        $country = $this->getElement('country');
        $country->selectOption($name);

        $this->getDocument()->waitFor(5, function () {
            return false;
        });
    }

    public function saveChanges()
    {
        $this->getElement('save_button')->press();
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_account_address_book_update';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return [
            'city' => '#sylius_address_city',
            'country' => '#sylius_address_countryCode',
            'first_name' => '#sylius_address_firstName',
            'last_name' => '#sylius_address_lastName',
            'postcode' => '#sylius_address_postcode',
            'province' => '#sylius_address_province',
            'save_button' => 'button:contains("Save changes")',
            'street' => '#sylius_address_street',
        ];
    }

    /**
     * @param int $timeout
     * @param string $elementName
     */
    private function waitForElement($timeout, $elementName)
    {
        $this->getDocument()->waitFor($timeout, function () use ($elementName){
            return $this->hasElement($elementName);
        });
    }
}
