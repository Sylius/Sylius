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

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CheckoutThankYouPageInterface extends PageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function hasThankYouMessageFor($name);

    /**
     * @param int $timeout
     *
     * @throws \InvalidArgumentException
     */
    public function waitForResponse($timeout);
}
