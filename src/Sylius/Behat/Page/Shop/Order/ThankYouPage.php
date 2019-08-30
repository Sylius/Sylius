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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

class ThankYouPage extends SymfonyPage implements ThankYouPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function goToTheChangePaymentMethodPage(): void
    {
        $this->getElement('payment_method_page')->click();
    }

    public function goToOrderDetailsInAccount(): void
    {
        $this->getElement('order_details_in_account')->click();
    }

    /**
     * {@inheritdoc}
     */
    public function hasThankYouMessage()
    {
        $thankYouMessage = $this->getElement('thank_you')->getText();

        return false !== strpos($thankYouMessage, 'Thank you!');
    }

    /**
     * {@inheritdoc}
     */
    public function getInstructions()
    {
        return $this->getElement('instructions')->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function hasInstructions()
    {
        return null !== $this->getDocument()->find('css', '#sylius-payment-method-instructions');
    }

    /**
     * {@inheritdoc}
     */
    public function hasChangePaymentMethodButton()
    {
        return null !== $this->getDocument()->find('css', '#payment-method-page');
    }

    public function hasRegistrationButton(): bool
    {
        return $this->getDocument()->hasLink('Create an account');
    }

    public function createAccount(): void
    {
        $this->getDocument()->clickLink('Create an account');
    }

    public function getRouteName(): string
    {
        return 'sylius_shop_thank_you';
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'instructions' => '#sylius-payment-method-instructions',
            'order_details_in_account' => '#sylius-show-order-in-account',
            'payment_method_page' => '#payment-method-page',
            'thank_you' => '#sylius-thank-you',
        ]);
    }
}
