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

namespace Sylius\Behat\Page\Shop\Order;

use Behat\Mink\Element\NodeElement;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ShowPage extends SymfonyPage implements ShowPageInterface
{
    public function hasPayAction(): bool
    {
        return $this->hasElement('pay_link');
    }

    public function pay(): void
    {
        $this->getElement('pay_link')->click();
    }

    public function getNotifications(): array
    {
        /** @var NodeElement[] $notificationElements */
        $notificationElements = $this->getDocument()->findAll('css', '[data-test-flash-messages]');
        $notifications = [];

        foreach ($notificationElements as $notificationElement) {
            $notifications[] = $notificationElement->getText();
        }

        return $notifications;
    }

    public function choosePaymentMethod(string $paymentMethodName): void
    {
        $paymentMethodElement = $this->getElement('payment_method', ['%name%' => $paymentMethodName]);
        $paymentMethodElement->selectOption($paymentMethodElement->getAttribute('value'));
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_order_show';
    }

    public function getAmountOfItems(): int
    {
        $paymentItems = $this->getDocument()->findAll('css', '[data-test-payment-item]');

        return count($paymentItems);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pay_link' => '[data-test-pay-link]',
            'payment_method' => '[data-test-payment-item]:contains("%name%") [data-test-payment-method-select]',
        ]);
    }
}
