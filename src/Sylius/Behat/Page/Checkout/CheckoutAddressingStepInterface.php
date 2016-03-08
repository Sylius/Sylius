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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CheckoutAddressingStepInterface extends PageInterface
{
    /**
     * @param array $addressingDetails
     *
     * @throws ElementNotFoundException
     */
    public function fillAddressingDetails(array $addressingDetails);

    /**
     * @throws ElementNotFoundException
     */
    public function continueCheckout();
}
