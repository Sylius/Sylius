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

namespace Sylius\Behat\Element\Admin\Customer;

use Sylius\Behat\Element\Admin\Crud\FormElementInterface as BaseFormElementInterface;

interface FormElementInterface extends BaseFormElementInterface
{
    public function specifyFirstName(string $name): void;

    public function specifyLastName(string $name): void;

    public function specifyEmail(string $email): void;

    public function specifyBirthday(string $birthday): void;

    public function specifyPassword(string $password): void;

    public function chooseGender(string $gender): void;

    public function chooseGroup(string $group): void;

    public function getFullName(): string;

    public function getFirstName(): string;

    public function getLastName(): string;

    public function enable(): void;

    public function disable(): void;

    public function getPassword(): string;

    public function subscribeToTheNewsletter(): void;

    public function isSubscribedToTheNewsletter(): bool;

    public function getGroupName(): string;

    public function verifyUser(): void;
}
