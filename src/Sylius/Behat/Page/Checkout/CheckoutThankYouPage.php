<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Checkout;

use Sylius\Behat\SymfonyPageObjectExtension\PageObject\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutThankYouPage extends SymfonyPage
{
    /**
     * @var array
     */
    protected $elements = [
        'thank you message' => '#thanks',
    ];

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasThankYouMessageFor($name)
    {
        $thankYouMessage = $this->getElement('thank you message')->getText();

        return false !== strpos($thankYouMessage, sprintf('Thank you %s', $name));
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return 'sylius_checkout_thank_you';
    }
}
