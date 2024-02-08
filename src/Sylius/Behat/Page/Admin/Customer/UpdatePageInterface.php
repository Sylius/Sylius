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

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function getFullName(): string;

    public function changeFirstName(string $firstName): void;

    public function getFirstName(): string;

    public function changeLastName(string $lastName): void;

    public function getLastName(): string;

    public function changeEmail(string $email): void;

    public function changePassword(string $password): void;

    public function getPassword(): string;

    public function subscribeToTheNewsletter(): void;

    public function isSubscribedToTheNewsletter(): bool;

    public function getGroupName(): string;

    public function verifyUser(): void;
}
