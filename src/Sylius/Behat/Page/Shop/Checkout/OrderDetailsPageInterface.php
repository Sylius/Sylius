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

use Sylius\Behat\Page\SymfonyPageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface OrderDetailsPageInterface extends SymfonyPageInterface
{
    /**
     * @return bool
     */
    public function hasPayAction();

    public function pay();

    /**
     * @param string $paymentMethodName
     */
    public function choosePaymentMethod($paymentMethodName);
}
