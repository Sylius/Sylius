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

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Addressing\Model\AddressInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    const TYPE_BILLING = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddress(AddressInterface $address)
    {
        $this->specifyAddress($address, UpdatePage::TYPE_BILLING);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddress(AddressInterface $address)
    {
        $this->specifyAddress($address, UpdatePage::TYPE_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    private function specifyAddress(AddressInterface $address, $addressType)
    {
        $this->specifyElementValue($addressType.'_first_name', $address->getFirstName());
        $this->specifyElementValue($addressType.'_last_name', $address->getLastName());
        $this->specifyElementValue($addressType.'_street', $address->getStreet());
        $this->specifyElementValue($addressType.'_city', $address->getCity());
        $this->specifyElementValue($addressType.'_postcode', $address->getPostcode());

        $this->chooseCountry($address->getCountryCode(), $addressType);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor($element, $message)
    {
        $foundElement = $this->getFieldElement($element);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $message === $validationMessage->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_city' => '#sylius_order_billingAddress_city',
            'billing_country' => '#sylius_order_billingAddress_countryCode',
            'billing_first_name' => '#sylius_order_billingAddress_firstName',
            'billing_last_name' => '#sylius_order_billingAddress_lastName',
            'billing_postcode' => '#sylius_order_billingAddress_postcode',
            'billing_street' => '#sylius_order_billingAddress_street',
            'shipping_city' => '#sylius_order_shippingAddress_city',
            'shipping_country' => '#sylius_order_shippingAddress_countryCode',
            'shipping_first_name' => '#sylius_order_shippingAddress_firstName',
            'shipping_last_name' => '#sylius_order_shippingAddress_lastName',
            'shipping_postcode' => '#sylius_order_shippingAddress_postcode',
            'shipping_street' => '#sylius_order_shippingAddress_street',
        ]);
    }

    /**
     * @param string $elementName
     * @param string $value
     *
     * @throws ElementNotFoundException
     */
    private function specifyElementValue($elementName, $value)
    {
        $this->getElement($elementName)->setValue($value);
    }

    /**
     * @param string $country
     * @param string $addressType
     *
     * @throws ElementNotFoundException
     */
    private function chooseCountry($country, $addressType)
    {
        $this->getElement($addressType.'_country')->selectOption((null !== $country) ? $country : 'Select');
    }

    /**
     * @param string $element
     *
     * @return NodeElement|null
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement($element)
    {
        $element = $this->getElement($element);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
