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

namespace Sylius\Behat\Page\Admin\Administrator;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable(): void;

    /**
     * @param string $username
     */
    public function specifyUsername(string $username): void;

    /**
     * @param string $email
     */
    public function specifyEmail(string $email): void;

    /**
     * @param string $password
     */
    public function specifyPassword(string $password): void;

    /**
     * @param string $localeCode
     */
    public function specifyLocale(string $localeCode): void;
}
