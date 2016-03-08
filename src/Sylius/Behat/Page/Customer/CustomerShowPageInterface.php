<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Customer;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CustomerShowPageInterface extends PageInterface
{
    /**
     * Checks if the customer on whose page we are currently on is registered,
     * if not throws an exception.
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function isRegistered();

    /**
     * Deletes the user on whose show page we are currently on.
     *
     * @throws ElementNotFoundException
     */
    public function deleteAccount();
}
