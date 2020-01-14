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

namespace Sylius\Behat\Page\Shop\Checkout;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class SelectShippingPage extends SymfonyPage implements SelectShippingPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_select_shipping';
    }

    public function selectShippingMethod(string $shippingMethod): void
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getElement('shipping_method_select', ['%shipping_method%' => $shippingMethod])->click();

            return;
        }

        $shippingMethodOptionElement = $this->getElement('shipping_method_option', ['%shipping_method%' => $shippingMethod]);
        $shippingMethodOptionElement->selectOption($shippingMethodOptionElement->getAttribute('value'));
    }

    public function getShippingMethods(): array
    {
        $inputs = $this->getSession()->getPage()->findAll('css', '[data-test-shipping-method-label]');

        $shippingMethods = [];
        foreach ($inputs as $input) {
            $shippingMethods[] = trim($input->getText());
        }

        return $shippingMethods;
    }

    public function getSelectedShippingMethodName(): ?string
    {
        $shippingMethods = $this->getSession()->getPage()->findAll('css', '[data-test-shipping-item]');

        /** @var NodeElement $shippingMethod */
        foreach ($shippingMethods as $shippingMethod) {
            if (null !== $shippingMethod->find('css', 'input:checked')) {
                return $shippingMethod->find('css', '[data-test-shipping-method-label]')->getText();
            }
        }

        return null;
    }

    public function hasNoShippingMethodsMessage(): bool
    {
        try {
            $this->getElement('order_cannot_be_shipped_message');
        } catch (ElementNotFoundException $exception) {
            return false;
        }

        return true;
    }

    public function hasShippingMethodFee(string $shippingMethodName, string $fee): bool
    {
        $feeElement = $this->getElement('shipping_method_fee', ['%shipping_method%' => $shippingMethodName])->getText();

        return false !== strpos($feeElement, $fee);
    }

    public function getItemSubtotal(string $itemName): string
    {
        $itemSlug = strtolower(str_replace('\"', '', str_replace(' ', '-', $itemName)));

        $subtotalTable = $this->getElement('checkout_subtotal');

        return $subtotalTable->find('css', sprintf('[data-test-item-subtotal="%s"]', $itemSlug))->getText();
    }

    public function nextStep(): void
    {
        $this->getElement('next_step')->press();
    }

    public function changeAddress(): void
    {
        $this->getDocument()->clickLink('Change address');
    }

    public function changeAddressByStepLabel(): void
    {
        $this->getElement('address')->click();
    }

    public function getPurchaserEmail(): string
    {
        return $this->getElement('purchaser_email')->getText();
    }

    public function getValidationMessageForShipment(): string
    {
        $foundElement = $this->getElement('shipment');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Items element');
        }

        $validationMessage = $foundElement->find('css', '[data-test-validation-error]');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '[data-test-validation-error]');
        }

        return $validationMessage->getText();
    }

    public function hasNoAvailableShippingMethodsWarning(): bool
    {
        return $this->hasElement('warning_no_shipping_methods');
    }

    public function isNextStepButtonUnavailable(): bool
    {
        return $this->getElement('next_step')->hasClass('disabled');
    }

    public function hasShippingMethod(string $shippingMethodName): bool
    {
        $inputs = $this->getSession()->getPage()->findAll('css', '[data-test-shipping-method-label]');

        $shippingMethods = [];
        foreach ($inputs as $input) {
            $shippingMethods[] = trim($input->getText());
        }

        return in_array($shippingMethodName, $shippingMethods);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'address' => '[data-test-step-address]',
            'checkout_subtotal' => '[data-test-checkout-subtotal]',
            'next_step' => '[data-test-next-step]',
            'order_cannot_be_shipped_message' => '[data-test-order-cannot-be-shipped]',
            'purchaser_email' => '[data-test-purchaser-name-or-email]',
            'shipment' => '[data-test-shipments]',
            'shipping_method' => '[data-test-shipping-method-select]',
            'shipping_method_fee' => '[data-test-shipping-item]:contains("%shipping_method%") [data-test-shipping-method-fee]',
            'shipping_method_select' => '[data-test-shipping-item]:contains("%shipping_method%") [data-test-shipping-method-checkbox]',
            'shipping_method_option' => '[data-test-shipping-item]:contains("%shipping_method%") [data-test-shipping-method-select]',
            'warning_no_shipping_methods' => '[data-test-order-cannot-be-shipped]',
        ]);
    }
}
