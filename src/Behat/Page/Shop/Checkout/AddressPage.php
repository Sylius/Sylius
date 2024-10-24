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

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Mink\Session;
use Sylius\Behat\Page\Shop\Page as ShopPage;
use Sylius\Behat\Service\JQueryHelper;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

class AddressPage extends ShopPage implements AddressPageInterface
{
    public const TYPE_BILLING = 'billing';

    public const TYPE_SHIPPING = 'shipping';

    public function __construct(
        Session $session,
        $minkParameters,
        RouterInterface $router,
        private AddressFactoryInterface $addressFactory,
    ) {
        parent::__construct($session, $minkParameters, $router);
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_address';
    }

    public function chooseDifferentShippingAddress(): void
    {
        $this->chooseDifferentAddress('shipping');
    }

    public function chooseDifferentBillingAddress(): void
    {
        $this->chooseDifferentAddress('billing');
    }

    public function isDifferentShippingAddressChecked(): bool
    {
        return $this->getElement('different_shipping_address')->isChecked();
    }

    public function isShippingAddressVisible(): bool
    {
        try {
            return $this->getElement('shipping_address')->isVisible();
        } catch (UnsupportedDriverActionException) {
            // it's visible by default and is being hidden with JS
            return true;
        }
    }

    public function checkInvalidCredentialsValidation(): bool
    {
        /** @var NodeElement $validationElement */
        $validationElement = $this->getDocument()->waitFor(3, function (): ?NodeElement {
            try {
                $validationElement = $this->getElement('login_validation_error');
            } catch (ElementNotFoundException) {
                return null;
            }

            return $validationElement;
        });

        return $validationElement->getText() === 'Invalid credentials.';
    }

    public function checkValidationMessageFor(string $element, string $message): bool
    {
        $foundElement = $this->getFieldElement($element);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        $validationMessage = $foundElement->find('css', '[data-test-validation-error]');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $message === $validationMessage->getText();
    }

    public function checkFormValidationMessage(string $message): bool
    {
        $formElement = $this->getElement('form');
        if (null === $formElement) {
            throw new ElementNotFoundException($this->getSession(), 'Form');
        }

        $validationMessage = $formElement->findAll('css', '[data-test-validation-error]');
        foreach ($validationMessage as $validationMessage) {
            if ($validationMessage->getText() === $message) {
                return true;
            }
        }

        return false;
    }

    public function specifyShippingAddress(AddressInterface $shippingAddress): void
    {
        $this->specifyAddress($shippingAddress, self::TYPE_SHIPPING);
    }

    public function selectShippingAddressProvince(string $province): void
    {
        $this->waitForElementUpdate('form');

        $this->getElement('shipping_country_province')->selectOption($province);
    }

    public function specifyBillingAddress(AddressInterface $billingAddress): void
    {
        $this->specifyAddress($billingAddress, self::TYPE_BILLING);
    }

    public function selectBillingAddressProvince(string $province): void
    {
        $this->waitForElementUpdate('form');

        $this->getElement('billing_country_province')->selectOption($province);
    }

    public function specifyEmail(?string $email): void
    {
        $this->getElement('customer_email')->setValue($email);
    }

    public function specifyBillingAddressFullName(string $fullName): void
    {
        $names = explode(' ', $fullName);

        $this->getElement('billing_first_name')->setValue($names[0]);
        $this->getElement('billing_last_name')->setValue($names[1]);
    }

    public function canSignIn(): bool
    {
        $this->waitForElementUpdate('form');

        return $this->hasElement('login_button');
    }

    public function signIn(): void
    {
        $this->waitForElementUpdate('form');

        try {
            $this->getElement('login_button')->press();
        } catch (ElementNotFoundException) {
            $this->getElement('login_button')->click();
        }

        $this->waitForLoginAction();
    }

    public function specifyPassword(string $password): void
    {
        $this->waitForElementUpdate('form');

        $this->getElement('login_password')->setValue($password);
    }

    public function getItemSubtotal(string $itemName): string
    {
        $itemSlug = strtolower(str_replace('\"', '', str_replace(' ', '-', $itemName)));

        $subtotalTable = $this->getElement('checkout_subtotal');

        return $subtotalTable->find('css', sprintf('[data-test-item-subtotal="%s"]', $itemSlug))->getText();
    }

    public function getShippingAddressCountry(): string
    {
        return $this->getElement('shipping_country')->find('css', 'option:selected')->getText();
    }

    public function getBillingAddressCountry(): string
    {
        return $this->getElement('billing_country')->find('css', 'option:selected')->getText();
    }

    public function nextStep(): void
    {
        $this->getElement('next_step')->press();
    }

    public function backToStore(): void
    {
        $this->getDocument()->clickLink('Back to store');
    }

    public function specifyBillingAddressProvince(string $provinceName): void
    {
        $this->waitForElementUpdate('form');

        $this->getElement('billing_province')->setValue($provinceName);
    }

    public function specifyShippingAddressProvince(string $provinceName): void
    {
        $this->waitForElementUpdate('form');

        $this->getElement('shipping_province')->setValue($provinceName);
    }

    public function hasShippingAddressInput(): bool
    {
        $this->waitForElementUpdate('form');

        return $this->hasElement('shipping_province');
    }

    public function hasEmailInput(): bool
    {
        return $this->hasElement('customer_email');
    }

    public function hasBillingAddressInput(): bool
    {
        $this->waitForElementUpdate('form');

        return $this->hasElement('billing_province');
    }

    public function selectShippingAddressFromAddressBook(AddressInterface $address): void
    {
        $this->getElement('shipping_address_book')->selectOption($address->getId());

        $this->waitForElementUpdate('form');
    }

    public function selectBillingAddressFromAddressBook(AddressInterface $address): void
    {
        $this->getElement('billing_address_book')->selectOption($address->getId());

        $this->waitForElementUpdate('form');
    }

    public function getPreFilledShippingAddress(): AddressInterface
    {
        return $this->getPreFilledAddress(self::TYPE_SHIPPING);
    }

    public function getPreFilledBillingAddress(): AddressInterface
    {
        return $this->getPreFilledAddress(self::TYPE_BILLING);
    }

    public function getAvailableShippingCountries(): array
    {
        return $this->getOptionsFromSelect($this->getElement('shipping_country'));
    }

    public function getAvailableBillingCountries(): array
    {
        return $this->getOptionsFromSelect($this->getElement('billing_country'));
    }

    public function waitForFormToStopLoading(): void
    {
        JQueryHelper::waitForFormToStopLoading($this->getDocument());
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_address_book' => '[data-test-billing-address] [data-test-address-book]',
            'billing_city' => '[data-test-billing-address] [data-test-city]',
            'billing_country' => '[data-test-billing-address] [data-test-country]',
            'billing_country_province' => '[data-test-billing-address] [data-test-province-code]',
            'billing_first_name' => '[data-test-billing-address] [data-test-first-name]',
            'billing_last_name' => '[data-test-billing-address] [data-test-last-name]',
            'billing_postcode' => '[data-test-billing-address] [data-test-postcode]',
            'billing_province' => '[data-test-billing-address] [data-test-province-name]',
            'billing_street' => '[data-test-billing-address] [data-test-street]',
            'checkout_subtotal' => '[data-test-checkout-subtotal]',
            'customer_email' => '[data-test-login-email]',
            'different_billing_address' => '[data-test-different-billing-address]',
            'different_shipping_address' => '[data-test-different-shipping-address]',
            'form' => '[data-live-name-value="sylius_shop:checkout:address:form"]',
            'login_button' => '[data-test-login-button]',
            'login_password' => '[data-test-password-input]',
            'login_validation_error' => '[data-test-login-validation-error]',
            'next_step' => '[data-test-next-step]',
            'shipping_address' => '[data-test-shipping-address]',
            'shipping_address_book' => '[data-test-shipping-address] [data-test-address-book]',
            'shipping_city' => '[data-test-shipping-address] [data-test-city]',
            'shipping_country' => '[data-test-shipping-address] [data-test-country]',
            'shipping_country_province' => '[data-test-shipping-address] [data-test-province-code]',
            'shipping_first_name' => '[data-test-shipping-address] [data-test-first-name]',
            'shipping_last_name' => '[data-test-shipping-address] [data-test-last-name]',
            'shipping_postcode' => '[data-test-shipping-address] [data-test-postcode]',
            'shipping_province' => '[data-test-shipping-address] [data-test-province-name]',
            'shipping_street' => '[data-test-shipping-address] [data-test-street]',
        ]);
    }

    /** @return string[] */
    private function getOptionsFromSelect(NodeElement $element): array
    {
        return array_map(
            /** @return string[] */
            static fn (NodeElement $element): string => $element->getText(),
            $element->findAll('css', 'option[value!=""]'),
        );
    }

    private function getPreFilledAddress(string $type): AddressInterface
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
        $this->waitForElementUpdate('form');

        try {
            $address->setProvinceName($this->getElement(sprintf('%s_province', $type))->getValue());
        } catch (ElementNotFoundException) {
            $address->setProvinceCode($this->getElement(sprintf('%s_country_province', $type))->getValue());
        }

        return $address;
    }

    private function specifyAddress(AddressInterface $address, string $type): void
    {
        $this->assertAddressType($type);

        $this->getElement(sprintf('%s_first_name', $type))->setValue($address->getFirstName());
        $this->getElement(sprintf('%s_last_name', $type))->setValue($address->getLastName());
        $this->getElement(sprintf('%s_street', $type))->setValue($address->getStreet());
        $this->getElement(sprintf('%s_country', $type))->selectOption($address->getCountryCode() ?: 'Select');
        $this->getElement(sprintf('%s_city', $type))->setValue($address->getCity());
        $this->getElement(sprintf('%s_postcode', $type))->setValue($address->getPostcode());

        if (null !== $address->getProvinceName()) {
            $this->waitForElementUpdate('form');
            $this->getElement(sprintf('%s_province', $type))->setValue($address->getProvinceName());
        }
        if (null !== $address->getProvinceCode()) {
            $this->waitForElementUpdate('form');
            $this->getElement(sprintf('%s_country_province', $type))->selectOption($address->getProvinceCode());
        }
    }

    private function getFieldElement(string $element): ?NodeElement
    {
        $element = $this->getElement($element);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }

    private function waitForLoginAction(): bool
    {
        return $this->getDocument()->waitFor(5, fn () => !$this->hasElement('login_password'));
    }

    private function assertAddressType(string $type): void
    {
        $availableTypes = [self::TYPE_BILLING, self::TYPE_SHIPPING];

        Assert::oneOf($type, $availableTypes, sprintf('There are only two available types %s, %s. %s given', self::TYPE_BILLING, self::TYPE_SHIPPING, $type));
    }

    private function chooseDifferentAddress(string $type): void
    {
        $elem = $this->getElement(sprintf('different_%s_address', $type));
        $elem->click();
        $this->waitForElementUpdate('form');
    }
}
