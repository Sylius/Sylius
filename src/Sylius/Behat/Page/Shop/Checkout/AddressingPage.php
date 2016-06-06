<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Intl\Intl;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddressingPage extends SymfonyPage implements AddressingPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_addressing';
    }

    /**
     * {@inheritdoc}
     */
    public function chooseDifferentBillingAddress()
    {
        $billingAddressSwitch = $this->getElement('different_billing_address');
        $this->assertCheckboxState($billingAddressSwitch, false);
        $billingAddressSwitch->check();
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
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.pointing');
        }

        return $message === $foundElement->find('css', '.pointing')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddressFirstName($firstName = null)
    {
        $this->getElement('shipping_first_name')->setValue($firstName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddressLastName($lastName = null)
    {
        $this->getElement('shipping_last_name')->setValue($lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddressStreet($streetName = null)
    {
        $this->getElement('shipping_street')->setValue($streetName);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseShippingAddressCountry($countryName = null)
    {
        $this->getElement('shipping_country')->selectOption($countryName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddressCity($cityName = null)
    {
        $this->getElement('shipping_city')->setValue($cityName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddressPostcode($postcode = null)
    {
        $this->getElement('shipping_postcode')->setValue($postcode);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddress(AddressInterface $shippingAddress)
    {
        $this->specifyShippingAddressFirstName($shippingAddress->getFirstName());
        $this->specifyShippingAddressLastName($shippingAddress->getLastName());
        $this->specifyShippingAddressStreet($shippingAddress->getStreet());
        $this->chooseShippingAddressCountry(Intl::getRegionBundle()->getCountryNames('en')[$shippingAddress->getCountryCode()]);
        $this->specifyShippingAddressCity($shippingAddress->getCity());
        $this->specifyShippingAddressPostcode($shippingAddress->getPostcode());
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddressFirstName($firstName = null)
    {
        $this->getElement('billing_first_name')->setValue($firstName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddressLastName($lastName = null)
    {
        $this->getElement('billing_last_name')->setValue($lastName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddressStreet($streetName = null)
    {
        $this->getElement('billing_street')->setValue($streetName);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseBillingAddressCountry($countryName = null)
    {
        $this->getElement('billing_country')->selectOption($countryName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddressCity($cityName = null)
    {
        $this->getElement('billing_city')->setValue($cityName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddressPostcode($postcode = null)
    {
        $this->getElement('billing_postcode')->setValue($postcode);
    }

    public function nextStep()
    {
        $this->getDocument()->pressButton('Next');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'shipping_first_name' => '#sylius_shop_checkout_addressing_shippingAddress_firstName',
            'shipping_last_name' => '#sylius_shop_checkout_addressing_shippingAddress_lastName',
            'shipping_street' => '#sylius_shop_checkout_addressing_shippingAddress_street',
            'shipping_country' => '#sylius_shop_checkout_addressing_shippingAddress_countryCode',
            'shipping_city' => '#sylius_shop_checkout_addressing_shippingAddress_city',
            'shipping_postcode' => '#sylius_shop_checkout_addressing_shippingAddress_postcode',
            'different_billing_address' => '#sylius_shop_checkout_addressing_differentBillingAddress',
            'billing_first_name' => '#sylius_shop_checkout_addressing_billingAddress_firstName',
            'billing_last_name' => '#sylius_shop_checkout_addressing_billingAddress_lastName',
            'billing_street' => '#sylius_shop_checkout_addressing_billingAddress_street',
            'billing_country' => '#sylius_shop_checkout_addressing_billingAddress_countryCode',
            'billing_city' => '#sylius_shop_checkout_addressing_billingAddress_city',
            'billing_postcode' => '#sylius_shop_checkout_addressing_billingAddress_postcode',
        ]);
    }

    /**
     * @param NodeElement $toggleableElement
     * @param bool $expectedState
     *
     * @throws \RuntimeException
     */
    private function assertCheckboxState(NodeElement $toggleableElement, $expectedState)
    {
        if ($toggleableElement->isChecked() !== $expectedState) {
            throw new \RuntimeException(sprintf('Toggleable element state %s but expected %s.', $toggleableElement->isChecked(), $expectedState));
        }
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
        while (null !== $element && !($element->hasClass('field'))) {
            $element = $element->getParent();
        }

        return $element;
    }
}
