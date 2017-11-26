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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @param string $firstName
     */
    public function changeFirstName(string $firstName): void;

    /**
     * @return string
     */
    public function getFirstName(): string;

    /**
     * @param string $lastName
     */
    public function changeLastName(string $lastName): void;

    /**
     * @return string
     */
    public function getLastName(): string;

    /**
     * @param string $email
     */
    public function changeEmail(string $email): void;

    /**
     * @param string $password
     */
    public function changePassword(string $password): void;

    /**
     * @return string
     */
    public function getPassword(): string;

    public function subscribeToTheNewsletter(): void;

    /**
     * @return bool
     */
    public function isSubscribedToTheNewsletter(): bool;

    /**
     * @return string
     */
    public function getGroupName(): string;
}
