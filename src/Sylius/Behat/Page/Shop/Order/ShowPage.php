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
    /**
     * {@inheritdoc}
     */
    public function hasPayAction()
    {
        return $this->hasElement('pay_link');
    }

    /**
     * {@inheritdoc}
     */
    public function pay()
    {
        $this->getElement('pay_link')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function getNotifications()
    {
        /** @var NodeElement[] $notificationElements */
        $notificationElements = $this->getDocument()->findAll('css', '.message > .content > p');
        $notifications = [];

        foreach ($notificationElements as $notificationElement) {
            $notifications[] = $notificationElement->getText();
        }

        return $notifications;
    }

    /**
     * {@inheritdoc}
     */
    public function choosePaymentMethod($paymentMethodName)
    {
        $paymentMethodElement = $this->getElement('payment_method', ['%name%' => $paymentMethodName]);
        $paymentMethodElement->selectOption($paymentMethodElement->getAttribute('value'));
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName(): string
    {
        return 'sylius_shop_order_show';
    }

    /**
     * {@inheritdoc}
     */
    public function getNumberOfItems(): int
    {
        $itemsText = trim($this->getElement('items_text')->getText());
        $itemsTextWords = explode(' ', $itemsText);

        return (int) $itemsTextWords[0];
    }

    /**
     * {@inheritdoc}
     */
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
