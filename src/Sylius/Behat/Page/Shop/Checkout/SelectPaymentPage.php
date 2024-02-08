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
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use Sylius\Behat\Service\DriverHelper;

class SelectPaymentPage extends SymfonyPage implements SelectPaymentPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_shop_checkout_select_payment';
    }

    public function selectPaymentMethod(string $paymentMethod): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getElement('payment_method_select', ['%payment_method%' => $paymentMethod])->click();

            return;
        }

        $paymentMethodOptionElement = $this->getElement('payment_method_option', ['%payment_method%' => $paymentMethod]);
        $paymentMethodOptionElement->selectOption($paymentMethodOptionElement->getAttribute('value'));
    }

    public function hasPaymentMethod(string $paymentMethodName): bool
    {
        try {
            $this->getElement('payment_method_option', ['%payment_method%' => $paymentMethodName]);
        } catch (ElementNotFoundException) {
            return false;
        }

        return true;
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

    public function changeShippingMethod(): void
    {
        $this->getDocument()->clickLink('Change shipping method');
    }

    public function changeShippingMethodByStepLabel(): void
    {
        $this->getElement('shipping_step_label')->click();
    }

    public function changeAddressByStepLabel(): void
    {
        $this->getElement('address')->click();
    }

    public function hasNoAvailablePaymentMethodsWarning(): bool
    {
        return $this->hasElement('warning_no_payment_methods');
    }

    public function isNextStepButtonUnavailable(): bool
    {
        return $this->getElement('next_step')->hasClass('disabled');
    }

    public function getPaymentMethods(): array
    {
        $inputs = $this->getSession()->getPage()->findAll('css', '[data-test-payment-method-label]');

        $paymentMethods = [];
        foreach ($inputs as $input) {
            $paymentMethods[] = trim($input->getText());
        }

        return $paymentMethods;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'address' => '[data-test-step-address]',
            'checkout_subtotal' => '[data-test-checkout-subtotal]',
            'next_step' => '[data-test-next-step]',
            'order_cannot_be_paid_message' => '[data-test-order-cannot-be-paid]',
            'payment_method_option' => '[data-test-payment-item]:contains("%payment_method%") [data-test-payment-method-select]',
            'payment_method_select' => '[data-test-payment-item]:contains("%payment_method%") [data-test-payment-method-checkbox]',
            'shipping_step_label' => '[data-test-step-shipping]',
            'warning_no_payment_methods' => '[data-test-order-cannot-be-paid]',
        ]);
    }
}
