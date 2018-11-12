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

interface RegisterPageInterface extends SymfonyPageInterface
{
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function register();

    public function specifyEmail(string $email);

    public function specifyFirstName(string $firstName);

    public function specifyLastName(string $lastName);

    public function specifyPassword(string $password);

    public function specifyPhoneNumber(string $phoneNumber);

    public function verifyPassword(string $password);

    public function subscribeToTheNewsletter();
}
