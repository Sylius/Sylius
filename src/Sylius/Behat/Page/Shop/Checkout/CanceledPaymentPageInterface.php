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

use Sylius\Behat\Page\PageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface CanceledPaymentPageInterface extends PageInterface
{
    public function clickPayButton();

    /**
     * @param int $timeout
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function waitForResponse($timeout, array $parameters = []);
}
