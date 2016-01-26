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

use Sylius\Behat\Page\SymfonyPage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class CheckoutThankYouPage extends SymfonyPage
{
    public function waitForPaypalRedirect()
    {
        $this->waitFor(10, function() {
            return $this->isOpen();
        });
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return 'sylius_checkout_thank_you';
    }
}
