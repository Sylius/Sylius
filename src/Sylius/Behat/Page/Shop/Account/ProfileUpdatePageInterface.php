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

interface ProfileUpdatePageInterface extends PageInterface
{
    /**
     * @param string $firstName
     */
    public function specifyFirstName(string $firstName): void;

    /**
     * @param string $lastName
     */
    public function specifyLastName(string $lastName): void;

    /**
     * @param string $email
     */
    public function specifyEmail(string $email): void;

    public function saveChanges(): void;

    /**
     * @param string $element
     * @param string $message
     *
     * @return bool
     *
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function subscribeToTheNewsletter(): void;

    /**
     * @return bool
     */
    public function isSubscribedToTheNewsletter(): bool;
}
