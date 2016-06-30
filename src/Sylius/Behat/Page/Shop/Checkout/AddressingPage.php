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
use Webmozart\Assert\Assert;

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
        Assert::false(
            $billingAddressSwitch->isChecked(),
            'Previous state of different billing address switch was true expected to be false'
        );

        $billingAddressSwitch->check();
    }

    /**
     * {@inheritdoc}
     */
    public function checkInvalidCredentialsValidation()
    {
        $this->getElement('login_password')->waitFor(5, function () {
            $validationElement = $this->getElement('login_password')->getParent()->find('css', '.red.label');
            if (null === $validationElement) {
                return false;
            }

            return $validationElement->isVisible();
        });

        return $this->checkValidationMessageFor('login_password', 'Invalid credentials.');
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

        $validationMessage = $foundElement->find('css', '.pointing');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.pointing');
        }

        return $message === $validationMessage->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddress(AddressInterface $shippingAddress)
    {
        $this->getElement('shipping_first_name')->setValue($shippingAddress->getFirstName());
        $this->getElement('shipping_last_name')->setValue($shippingAddress->getLastName());
        $this->getElement('shipping_street')->setValue($shippingAddress->getStreet());
        $this->getElement('shipping_country')->selectOption($this->getCountryNameOrDefault($shippingAddress->getCountryCode()));
        $this->getElement('shipping_city')->setValue($shippingAddress->getCity());
        $this->getElement('shipping_postcode')->setValue($shippingAddress->getPostcode());
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddress(AddressInterface $billingAddress)
    {
        $this->getElement('billing_first_name')->setValue($billingAddress->getFirstName());
        $this->getElement('billing_last_name')->setValue($billingAddress->getLastName());
        $this->getElement('billing_street')->setValue($billingAddress->getStreet());
        $this->getElement('billing_country')->selectOption($this->getCountryNameOrDefault($billingAddress->getCountryCode()));
        $this->getElement('billing_city')->setValue($billingAddress->getCity());
        $this->getElement('billing_postcode')->setValue($billingAddress->getPostcode());
    }

    /**
     * {@inheritdoc}
     */
    public function specifyEmail($email)
    {
        $this->getElement('customer_email')->setValue($email);
    }

    /**
     * {@inheritdoc}
     */
    public function canSignIn()
    {
        return $this->isSignInActionAvailable();
    }

    /**
     * {@inheritdoc}
     */
    public function signIn()
    {
        $this->isSignInActionAvailable();
        try {
            $this->getElement('login_button')->press();
        } catch (ElementNotFoundException $elementNotFoundException) {
            $this->getElement('login_button')->click();
        }

        $this->waitForLoginAction();
    }

    /**
     * {@inheritdoc}
     */
    public function specifyPassword($password)
    {
        $this->getDocument()->waitFor(5, function () {
            return $this->getElement('login_password')->isVisible();
        });

        $this->getElement('login_password')->setValue($password);
    }

    public function nextStep()
    {
        $this->getDocument()->pressButton('Next');
    }

    public function backToStore()
    {
        $this->getDocument()->clickLink('Back to store');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_first_name' => '#sylius_shop_checkout_addressing_billingAddress_firstName',
            'billing_last_name' => '#sylius_shop_checkout_addressing_billingAddress_lastName',
            'billing_street' => '#sylius_shop_checkout_addressing_billingAddress_street',
            'billing_country' => '#sylius_shop_checkout_addressing_billingAddress_countryCode',
            'billing_city' => '#sylius_shop_checkout_addressing_billingAddress_city',
            'billing_postcode' => '#sylius_shop_checkout_addressing_billingAddress_postcode',
            'customer_email' => '#sylius_shop_checkout_addressing_customer_email',
            'different_billing_address' => '#sylius_shop_checkout_addressing_differentBillingAddress',
            'shipping_first_name' => '#sylius_shop_checkout_addressing_shippingAddress_firstName',
            'shipping_last_name' => '#sylius_shop_checkout_addressing_shippingAddress_lastName',
            'shipping_street' => '#sylius_shop_checkout_addressing_shippingAddress_street',
            'shipping_country' => '#sylius_shop_checkout_addressing_shippingAddress_countryCode',
            'shipping_city' => '#sylius_shop_checkout_addressing_shippingAddress_city',
            'shipping_postcode' => '#sylius_shop_checkout_addressing_shippingAddress_postcode',
            'login_password' => 'input[type=\'password\']',
            'login_button' => '#sylius-api-login-submit',
        ]);
    }

    /**
     * @param string|null $code
     * 
     * @return string
     */
    private function getCountryNameOrDefault($code)
    {
        $countryName = null === $code ? 'Select' : Intl::getRegionBundle()->getCountryNames('en')[$code];

        return $countryName;
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

    /**
     * @return bool
     */
    private function isSignInActionAvailable()
    {
        return $this->getDocument()->waitFor(5, function () {
            return $this->hasElement('login_button');
        });
    }

    private function waitForLoginAction()
    {
        $this->getDocument()->waitFor(5, function () {
            return !$this->hasElement('login_password');
        });
    }
}
