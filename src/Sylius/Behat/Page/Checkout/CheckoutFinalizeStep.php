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
class CheckoutFinalizeStep extends SymfonyPage
{
    public function confirmOrder()
    {
        $this->getDocument()->clickLink('Place order');
    }

    /**
     * @return string
     */
    protected function getRouteName()
    {
        return 'sylius_checkout_finalize';
    }
}
