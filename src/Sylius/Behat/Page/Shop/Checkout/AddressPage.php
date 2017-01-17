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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class AddressPage extends SymfonyPage implements AddressPageInterface
{
    const TYPE_BILLING = 'billing';
    const TYPE_SHIPPING = 'shipping';

    /**
     * @var AddressFactoryInterface
     */
    private $addressFactory;

    /**
     * @param Session $session
     * @param array $parameters
     * @param RouterInterface $router
     * @param AddressFactoryInterface $addressFactory
     */
    public function __construct(
        Session $session,
        array $parameters,
        RouterInterface $router,
        AddressFactoryInterface $addressFactory
    ) {
        parent::__construct($session, $parameters, $router);

        $this->addressFactory = $addressFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_shop_checkout_address';
    }

    /**
     * {@inheritdoc}
     */
    public function chooseDifferentBillingAddress()
    {
        $driver = $this->getDriver();
        if ($driver instanceof Selenium2Driver) {
            $this->getElement('different_billing_address_label')->click();

            return;
        }

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
    public function specifyShippingAddress(AddressInterface $shippingAddress)
    {
        $this->specifyAddress($shippingAddress, self::TYPE_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function selectShippingAddressProvince($province)
    {
        $this->waitForElement(5, 'shipping_country_province');
        $this->getElement('shipping_country_province')->selectOption($province);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddress(AddressInterface $billingAddress)
    {
        $this->specifyAddress($billingAddress, self::TYPE_BILLING);
    }

    /**
     * {@inheritdoc}
     */
    public function selectBillingAddressProvince($province)
    {
        $this->waitForElement(5, 'billing_country_province');
        $this->getElement('billing_country_province')->selectOption($province);
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
        return $this->waitForElement(5, 'login_button');
    }

    /**
     * {@inheritdoc}
     */
    public function signIn()
    {
        $this->waitForElement(5, 'login_button');
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

    /**
     * {@inheritdoc}
     */
    public function getItemSubtotal($itemName)
    {
        $itemSlug = strtolower(str_replace('\"', '', str_replace(' ', '-', $itemName)));

        $subtotalTable = $this->getElement('checkout_subtotal');

        return $subtotalTable->find('css', sprintf('#sylius-item-%s-subtotal', $itemSlug))->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddressCountry()
    {
        return $this->getElement('shipping_country')->find('css', 'option:selected')->getText();
    }

    public function nextStep()
    {
        $this->getElement('next_step')->press();
    }

    public function backToStore()
    {
        $this->getDocument()->clickLink('Back to store');
    }

    /**
     * {@inheritdoc}
     */
    public function specifyBillingAddressProvince($provinceName)
    {
        $this->waitForElement(5, 'billing_province');
        $this->getElement('billing_province')->setValue($provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyShippingAddressProvince($provinceName)
    {
        $this->waitForElement(5, 'shipping_province');
        $this->getElement('shipping_province')->setValue($provinceName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasShippingAddressInput()
    {
        return $this->waitForElement(5, 'shipping_province');
    }

    /**
     * {@inheritdoc}
     */
    public function hasBillingAddressInput()
    {
        return $this->waitForElement(5, 'billing_province');
    }

    /**
     * {@inheritdoc}
     */
    public function selectShippingAddressFromAddressBook(AddressInterface $address)
    {
        $this->waitForElement(2, sprintf('%s_province', self::TYPE_SHIPPING));
        $addressBookSelect = $this->getElement('shipping_address_book');

        $addressBookSelect->click();
        $addressOption = $addressBookSelect->waitFor(5, function () use ($address, $addressBookSelect) {
            return $addressBookSelect->find('css', sprintf('.item[data-id="%s"]', $address->getId()));
        });

        if (null === $addressOption) {
            throw new ElementNotFoundException($this->getDriver(), 'option', 'css', sprintf('.item[data-id="%s"]', $address->getId()));
        }

        $addressOption->click();
    }

    /**
     * {@inheritdoc}
     */
    public function selectBillingAddressFromAddressBook(AddressInterface $address)
    {
        $this->waitForElement(2, sprintf('%s_province', self::TYPE_BILLING));
        $addressBookSelect = $this->getElement('billing_address_book');

        $addressBookSelect->click();
        $addressOption = $addressBookSelect->waitFor(5, function () use ($address, $addressBookSelect) {
            return $addressBookSelect->find('css', sprintf('.item[data-id="%s"]', $address->getId()));
        });

        if (null === $addressOption) {
            throw new ElementNotFoundException($this->getDriver(), 'option', 'css', sprintf('.item[data-id="%s"]', $address->getId()));
        }

        $addressOption->click();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreFilledShippingAddress()
    {
        return $this->getPreFilledAddress(self::TYPE_SHIPPING);
    }

    /**
     * {@inheritdoc}
     */
    public function getPreFilledBillingAddress()
    {
        return $this->getPreFilledAddress(self::TYPE_BILLING);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address_book' => '#sylius-billing-address .ui.dropdown',
            'billing_first_name' => '#sylius_checkout_address_billingAddress_firstName',
            'billing_last_name' => '#sylius_checkout_address_billingAddress_lastName',
            'billing_street' => '#sylius_checkout_address_billingAddress_street',
            'billing_city' => '#sylius_checkout_address_billingAddress_city',
            'billing_country' => '#sylius_checkout_address_billingAddress_countryCode',
            'billing_country_province' => '[name="sylius_checkout_address[billingAddress][provinceCode]"]',
            'billing_postcode' => '#sylius_checkout_address_billingAddress_postcode',
            'billing_province' => '[name="sylius_checkout_address[billingAddress][provinceName]"]',
            'checkout_subtotal' => '#sylius-checkout-subtotal',
            'customer_email' => '#sylius_checkout_address_customer_email',
            'different_billing_address' => '#sylius_checkout_address_differentBillingAddress',
            'different_billing_address_label' => '#sylius_checkout_address_differentBillingAddress ~ label',
            'login_button' => '#sylius-api-login-submit',
            'login_password' => 'input[type=\'password\']',
            'next_step' => '#next-step',
            'shipping_address_book' => '#sylius-shipping-address .ui.dropdown',
            'shipping_city' => '#sylius_checkout_address_shippingAddress_city',
            'shipping_country' => '#sylius_checkout_address_shippingAddress_countryCode',
            'shipping_country_province' => '[name="sylius_checkout_address[shippingAddress][provinceCode]"]',
            'shipping_first_name' => '#sylius_checkout_address_shippingAddress_firstName',
            'shipping_last_name' => '#sylius_checkout_address_shippingAddress_lastName',
            'shipping_postcode' => '#sylius_checkout_address_shippingAddress_postcode',
            'shipping_province' => '[name="sylius_checkout_address[shippingAddress][provinceName]"]',
            'shipping_street' => '#sylius_checkout_address_shippingAddress_street',
        ]);
    }

    /**
     * @param string $type
     *
     * @return AddressInterface
     */
    private function getPreFilledAddress($type)
    {
        $this->assertAddressType($type);

        /** @var AddressInterface $address */
        $address = $this->addressFactory->createNew();

        $address->setFirstName($this->getElement(sprintf('%s_first_name', $type))->getValue());
        $address->setLastName($this->getElement(sprintf('%s_last_name', $type))->getValue());
        $address->setStreet($this->getElement(sprintf('%s_street', $type))->getValue());
        $address->setCountryCode($this->getElement(sprintf('%s_country', $type))->getValue());
        $address->setCity($this->getElement(sprintf('%s_city', $type))->getValue());
        $address->setPostcode($this->getElement(sprintf('%s_postcode', $type))->getValue());
        $this->waitForElement(5, sprintf('%s_province', $type));
        try {
            $address->setProvinceName($this->getElement(sprintf('%s_province', $type))->getValue());
        } catch (ElementNotFoundException $exception) {
            $address->setProvinceCode($this->getElement(sprintf('%s_country_province', $type))->getValue());
        }

        return $address;
    }

    /**
     * @param AddressInterface $address
     * @param string $type
     */
    private function specifyAddress(AddressInterface $address, $type)
    {
        $this->assertAddressType($type);

        $this->getElement(sprintf('%s_first_name', $type))->setValue($address->getFirstName());
        $this->getElement(sprintf('%s_last_name', $type))->setValue($address->getLastName());
        $this->getElement(sprintf('%s_street', $type))->setValue($address->getStreet());
        $this->getElement(sprintf('%s_country', $type))->selectOption($address->getCountryCode() ?: 'Select');
        $this->getElement(sprintf('%s_city', $type))->setValue($address->getCity());
        $this->getElement(sprintf('%s_postcode', $type))->setValue($address->getPostcode());

        if (null !== $address->getProvinceName()) {
            $this->waitForElement(5, sprintf('%s_province', $type));
            $this->getElement(sprintf('%s_province', $type))->setValue($address->getProvinceName());
        }
        if (null !== $address->getProvinceCode()) {
            $this->waitForElement(5, sprintf('%s_country_province', $type));
            $this->getElement(sprintf('%s_country_province', $type))->selectOption($address->getProvinceCode());
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
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }

    /**
     * @return bool
     */
    private function waitForLoginAction()
    {
        return $this->getDocument()->waitFor(5, function () {
            return !$this->hasElement('login_password');
        });
    }

    /**
     * @return bool
     */
    private function waitForElement($timeout, $elementName)
    {
        return $this->getDocument()->waitFor($timeout, function () use ($elementName) {
            return $this->hasElement($elementName);
        });
    }

    /**
     * @param string $type
     */
    private function assertAddressType($type)
    {
        $availableTypes = [self::TYPE_BILLING, self::TYPE_SHIPPING];

        Assert::oneOf($type, $availableTypes, sprintf('There are only two available types %s, %s. %s given', self::TYPE_BILLING, self::TYPE_SHIPPING, $type));
    }
}
