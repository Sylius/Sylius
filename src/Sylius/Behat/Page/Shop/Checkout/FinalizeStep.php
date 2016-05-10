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
class FinalizeStep extends SymfonyPage implements FinalizeStepInterface
{
    /**
     * {@inheritdoc}
     */
    public function confirmOrder()
    {
        $this->getDocument()->clickLink('Place order');
    }

    /**
     * @return string
     */
    public function getRouteName()
    {
        return 'sylius_checkout_finalize';
    }
}
