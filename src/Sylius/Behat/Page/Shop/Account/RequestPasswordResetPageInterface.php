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

use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;

interface RequestPasswordResetPageInterface extends SymfonyPageInterface
{
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function reset();

    /**
     * @param string $email
     */
    public function specifyEmail($email);
}
