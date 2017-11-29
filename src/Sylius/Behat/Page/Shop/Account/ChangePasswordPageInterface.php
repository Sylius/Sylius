<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\PageInterface;

interface ChangePasswordPageInterface extends PageInterface
{
    /**
     * @param string $password
     */
    public function specifyCurrentPassword(string $password): void;

    /**
     * @param string $password
     */
    public function specifyNewPassword(string $password): void;

    /**
     * @param string $password
     */
    public function specifyConfirmationPassword(string $password): void;

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;
}
