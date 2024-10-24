<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Shop\Account;

use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\PageInterface;

interface ChangePasswordPageInterface extends PageInterface
{
    public function specifyCurrentPassword(string $password): void;

    public function specifyNewPassword(string $password): void;

    public function specifyConfirmationPassword(string $password): void;

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;
}
