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
    public function enable();

    public function disable();

    public function getFullName(): string;

    public function changeFirstName(string $firstName);

    public function getFirstName(): string;

    public function changeLastName(string $lastName);

    public function getLastName(): string;

    public function changeEmail(string $email);

    public function changePassword(string $password);

    public function getPassword(): string;

    public function subscribeToTheNewsletter();

    public function isSubscribedToTheNewsletter(): bool;

    public function getGroupName(): string;
}
