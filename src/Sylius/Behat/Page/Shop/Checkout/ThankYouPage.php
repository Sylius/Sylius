<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ThankYouPage extends SymfonyPage implements ThankYouPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasThankYouMessageFor($name)
    {
        $thankYouMessage = $this->getElement('thank you message')->getText();

        return false !== strpos($thankYouMessage, sprintf('Thank you %s', $name));
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
        return 'sylius_checkout_thank_you';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'thank you message' => '#thanks',
        ]);
    }
}
