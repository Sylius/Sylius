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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface SecurityStepInterface extends PageInterface
{
    /**
     * @param string $login
     * @param string $password
     *
     * @throws ElementNotFoundException
     */
    public function logInAsExistingUser($login, $password);

    /**
     * @param string $email
     *
     * @throws ElementNotFoundException
     */
    public function proceedAsGuest($email);
}
