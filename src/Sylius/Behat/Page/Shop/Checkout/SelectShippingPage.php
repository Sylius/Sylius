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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\SymfonyPage;
use Sylius\Behat\Service\DriverHelper;

class SelectShippingPage extends SymfonyPage implements SelectShippingPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_select_shipping';
    }

    public function selectShippingMethod(string $shippingMethod): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            DriverHelper::waitForPageToLoad($this->getSession());
            $this->getElement('shipping_method_select', ['%shipping_method%' => $shippingMethod])->click();

            return;
        }

        $shippingMethodOptionElement = $this->getElement('shipping_method_option', ['%shipping_method%' => $shippingMethod]);
        $shippingMethodOptionElement->selectOption($shippingMethodOptionElement->getAttribute('value'));
    }

    public function getShippingMethods(): array
    {
        $inputs = $this->getDocument()->findAll('css', '[data-test-shipping-method-select]');

        $shippingMethods = [];
        foreach ($inputs as $input) {
            $shippingMethods[] = trim($input->getParent()->getText());
        }

        return $shippingMethods;
    }

    public function getSelectedShippingMethodName(): ?string
    {
        return $this->hasElement('shipping_method_option_selected')
            ? $this->getElement('shipping_method_option_selected')->getParent()->getText()
            : null
        ;
    }

    public function hasShippingMethodFee(string $shippingMethodName, string $fee): bool
    {
        $feeElement = $this->getElement('shipping_method_fee', ['%shipping_method%' => $shippingMethodName])->getText();

        return str_contains($feeElement, $fee);
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
        DriverHelper::waitForPageToLoad($this->getSession());
    }

    public function changeAddress(): void
    {
        $this->getDocument()->clickLink('Change address');
    }

    public function changeAddressByStepLabel(): void
    {
        $this->getElement('address')->click();
    }

    public function getPurchaserIdentifier(): string
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

    public function hasNoAvailableShippingMethodsMessage(): bool
    {
        return $this->hasElement('warning_no_shipping_methods');
    }

    public function isNextStepButtonEnabled(): bool
    {
        return !$this->getElement('next_step')->hasClass('disabled');
    }

    public function hasShippingMethod(string $shippingMethodName): bool
    {
        return $this->hasElement('shipping_method_item', ['%shipping_method%' => $shippingMethodName]);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'address' => '[data-test-step-address]',
            'checkout_subtotal' => '[data-test-checkout-subtotal]',
            'next_step' => '[data-test-next-step]',
            'purchaser_email' => '[data-test-purchaser-name-or-email]',
            'shipping_method_fee' => '[data-test-shipping-item]:contains("%shipping_method%") [data-test-shipping-method-fee]',
            'shipping_method_item' => '[data-test-shipping-item]:contains("%shipping_method%")',
            'shipping_method_option' => '[data-test-shipping-item]:contains("%shipping_method%") [data-test-shipping-method-select]',
            'shipping_method_option_selected' => '[data-test-shipping-method-select][checked="checked"]',
            'shipping_method_select' => '[data-test-shipping-item]:contains("%shipping_method%") [data-test-shipping-method-checkbox]',
            'warning_no_shipping_methods' => '[data-test-order-cannot-be-shipped]',
        ]);
    }
}
