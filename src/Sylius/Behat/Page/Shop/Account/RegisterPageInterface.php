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
    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function register(): void;

    /**
     * @param string $email
     */
    public function specifyEmail(string $email): void;

    /**
     * @param string $firstName
     */
    public function specifyFirstName(string $firstName): void;

    /**
     * @param string $lastName
     */
    public function specifyLastName(string $lastName): void;

    /**
     * @param string $password
     */
    public function specifyPassword(string $password): void;

    /**
     * @param string $phoneNumber
     */
    public function specifyPhoneNumber(string $phoneNumber): void;

    /**
     * @param string $password
     */
    public function verifyPassword(string $password): void;

    public function subscribeToTheNewsletter(): void;
}
