<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Order;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ThankYouPage extends SymfonyPage implements ThankYouPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function goToOrderDetails()
    {
        $this->getElement('order_details')->click();
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
        return null !== $this->getDocument()->find('css', '#sylius-show-order');
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_shop_thank_you';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'order_details' => '#sylius-show-order',
            'instructions' => '#sylius-payment-method-instructions',
            'thank_you' => '#sylius-thank-you',
        ]);
    }
}
