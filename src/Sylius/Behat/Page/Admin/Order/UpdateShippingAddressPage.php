<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Order;

use Sylius\Behat\Page\Admin\Crud\UpdatePage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UpdateShippingAddressPage extends UpdatePage implements UpdateShippingAddressPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return sprintf('sylius_admin_%s_shipping_address_update', $this->getResourceName());
    }

    /**
     * {@inheritdoc}
     */
    public function specifyFirstName($firstName)
    {
        $this->getDocument()->fillField('First name', $firstName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyLastName($lastName)
    {
        $this->getDocument()->fillField('Last name', $lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyStreet($street)
    {
        $this->getDocument()->fillField('Street', $street);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyCity($city)
    {
        $this->getDocument()->fillField('City', $city);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPostcode($postcode)
    {
        $this->getDocument()->fillField('Postcode', $postcode);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseCountry($country)
    {
        $this->getDocument()->selectFieldOption('Country', $country);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddress($city, $street, $postcode, $country, $firstAndLastName)
    {
        $customerNames = explode(' ', $firstAndLastName);
        $this->specifyFirstName($customerNames[0]);
        if (1 < count($customerNames)) {
            $this->specifyLastName($customerNames[1]);
        }

        $this->specifyStreet($street);
        $this->specifyCity($city);
        $this->specifyPostcode($postcode);
        $this->chooseCountry($country);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'city' => '#sylius_order_shippingAddress_city',
            'first_name' => '#sylius_order_shippingAddress_firstName',
            'last_name' => '#sylius_order_shippingAddress_lastName',
            'street' => '#sylius_order_shippingAddress_street',
        ]);
    }
}
