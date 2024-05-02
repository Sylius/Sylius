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

interface ProfileUpdatePageInterface extends PageInterface
{
    public function specifyFirstName(?string $firstName): void;

    public function specifyPhoneNumber(?string $phoneNumber): void;

    public function getPhoneNumber(): string;

    public function specifyLastName(?string $lastName): void;

    public function specifyEmail(?string $email): void;

    public function saveChanges(): void;

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function subscribeToTheNewsletter(): void;

    public function isSubscribedToTheNewsletter(): bool;
}
