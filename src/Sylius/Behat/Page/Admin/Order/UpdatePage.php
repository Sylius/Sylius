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

namespace Sylius\Behat\Page\Admin\Order;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Sylius\Component\Addressing\Model\AddressInterface;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public const TYPE_BILLING = 'billing';

    public const TYPE_SHIPPING = 'shipping';

    public function specifyBillingAddress(AddressInterface $address): void
    {
        $this->specifyAddress($address, self::TYPE_BILLING);
    }

    public function specifyShippingAddress(AddressInterface $address): void
    {
        $this->specifyAddress($address, self::TYPE_SHIPPING);
    }

    private function specifyAddress(AddressInterface $address, $addressType): void
    {
        $this->specifyElementValue($addressType . '_first_name', $address->getFirstName());
        $this->specifyElementValue($addressType . '_last_name', $address->getLastName());
        $this->specifyElementValue($addressType . '_street', $address->getStreet());
        $this->specifyElementValue($addressType . '_city', $address->getCity());
        $this->specifyElementValue($addressType . '_postcode', $address->getPostcode());

        $this->chooseCountry($address->getCountryCode(), $addressType);
    }

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool
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

    protected function getDefinedElements(): array
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
     * @throws ElementNotFoundException
     */
    private function specifyElementValue(string $elementName, ?string $value): void
    {
        $this->getElement($elementName)->setValue($value);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function chooseCountry(?string $country, string $addressType): void
    {
        $this->getElement($addressType . '_country')->selectOption($country ?? 'Select');
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element): ?NodeElement
    {
        $element = $this->getElement($element);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
