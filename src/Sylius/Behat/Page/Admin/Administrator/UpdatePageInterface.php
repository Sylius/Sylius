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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $username
     */
    public function changeUsername(string $username): void;

    /**
     * @param string $email
     */
    public function changeEmail(string $email): void;

    /**
     * @param string $password
     */
    public function changePassword(string $password): void;

    /**
     * @param string $localeCode
     */
    public function changeLocale(string $localeCode): void;
}
