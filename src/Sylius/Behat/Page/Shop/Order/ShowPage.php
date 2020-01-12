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

    /**
     * @return string[]
     */
    public function getNotifications(): array
    {
        /** @var NodeElement[] $notificationElements */
        $notificationElements = $this->getDocument()->findAll('css', '.message > .content > p');
        $notifications = [];

        foreach ($notificationElements as $notificationElement) {
            $notifications[] = $notificationElement->getText();
        }

        return $notifications;
    }

    public function choosePaymentMethod($paymentMethodName): void
    {
        $paymentMethodElement = $this->getElement('payment_method', ['%name%' => $paymentMethodName]);
        $paymentMethodElement->selectOption($paymentMethodElement->getAttribute('value'));
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_order_show';
    }

    public function getNumberOfItems(): int
    {
        $itemsText = trim($this->getElement('items_text')->getText());
        $itemsTextWords = explode(' ', $itemsText);

        return (int) $itemsTextWords[0];
    }
    
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'instructions' => '#sylius-payment-method-instructions',
            'items_text' => 'div.sub.header div.item:nth-child(3)',
            'pay_link' => '#sylius-pay-link',
            'payment_method' => '.item:contains("%name%") input',
            'thank_you' => '#sylius-thank-you',
        ]);
    }
}
