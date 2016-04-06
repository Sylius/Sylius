<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Customer;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface ShowPageInterface extends PageInterface
{
    /**
     * Checks if the customer on whose page we are currently on is registered,
     * if not throws an exception.
     *
     * @return bool
     */
    public function isRegistered();

    /**
     * Deletes the user on whose show page we are currently on.
     *
     * @throws ElementNotFoundException If there is no delete account button on the page
     */
    public function deleteAccount();
}
