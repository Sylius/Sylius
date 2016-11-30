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
    public function waitForResponse($timeout, array $parameters = [])
    {
        $this->getDocument()->waitFor($timeout, function () use ($parameters) {
            return $this->isOpen($parameters);
        });
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
            'instructions' => '#sylius-payment-method-instructions',
            'thank_you' => '#sylius-thank-you',
        ]);
    }
}
