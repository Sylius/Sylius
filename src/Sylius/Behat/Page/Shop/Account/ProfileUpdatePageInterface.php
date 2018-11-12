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
    public function specifyFirstName(string $firstName);

    public function specifyLastName(string $lastName);

    public function specifyEmail(string $email);

    public function saveChanges();

    /**
     * @throws ElementNotFoundException
     */
    public function checkValidationMessageFor(string $element, string $message): bool;

    public function subscribeToTheNewsletter();

    public function isSubscribedToTheNewsletter(): bool;
}
