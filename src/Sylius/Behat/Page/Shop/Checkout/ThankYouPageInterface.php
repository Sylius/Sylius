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
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ThankYouPageInterface extends PageInterface
{
    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasThankYouMessageFor($name);

    /**
     * @param int $timeout
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function waitForResponse($timeout, array $parameters = []);
}
