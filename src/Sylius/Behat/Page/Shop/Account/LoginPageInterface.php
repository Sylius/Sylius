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

use Sylius\Behat\Page\SymfonyPageInterface;

interface LoginPageInterface extends SymfonyPageInterface
{
    public function hasValidationErrorWith(string $message): bool;

    public function logIn();

    public function specifyPassword(string $password);

    public function specifyUsername(string $username);
}
