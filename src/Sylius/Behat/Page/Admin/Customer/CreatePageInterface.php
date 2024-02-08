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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function specifyFirstName(string $name): void;

    public function specifyLastName(string $name): void;

    public function specifyEmail(string $email): void;

    public function specifyBirthday(string $birthday): void;

    public function specifyPassword(string $password): void;

    public function chooseGender(string $gender): void;

    public function chooseGroup(string $group): void;

    public function selectCreateAccount(): void;

    public function hasPasswordField(): bool;

    public function hasCheckedCreateOption(): bool;

    public function hasCreateOption(): bool;

    public function isUserFormHidden(): bool;
}
