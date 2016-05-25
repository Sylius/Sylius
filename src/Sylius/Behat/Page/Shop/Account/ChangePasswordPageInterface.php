<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface ChangePasswordPageInterface extends PageInterface
{
    /**
     * @param string $password
     */
    public function specifyCurrentPassword($password);

    /**
     * @param string $password
     */
    public function specifyNewPassword($password);

    /**
     * @param string $password
     */
    public function specifyConfirmationPassword($password);

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor($element, $message);
}
