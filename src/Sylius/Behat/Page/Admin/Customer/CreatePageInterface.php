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

namespace Sylius\Behat\Page\Admin\Customer;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $name
     */
    public function specifyFirstName(string $name): void;

    /**
     * @param string $name
     */
    public function specifyLastName(string $name): void;

    /**
     * @param string $email
     */
    public function specifyEmail(string $email): void;

    /**
     * @param string $birthday
     */
    public function specifyBirthday(string $birthday): void;

    /**
     * @param string $password
     */
    public function specifyPassword(string $password): void;

    /**
     * @param string $gender
     */
    public function chooseGender(string $gender): void;

    /**
     * @param string $group
     */
    public function chooseGroup(string $group): void;

    public function selectCreateAccount(): void;

    /**
     * @return bool
     */
    public function hasPasswordField(): bool;

    /**
     * @return bool
     */
    public function hasCheckedCreateOption(): bool;

    /**
     * @return bool
     */
    public function isUserFormHidden(): bool;
}
