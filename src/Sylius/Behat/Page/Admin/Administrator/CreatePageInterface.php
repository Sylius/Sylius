<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Administrator;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable();

    /**
     * @param string $username
     */
    public function specifyUsername($username);

    /**
     * @param string $email
     */
    public function specifyEmail($email);

    /**
     * @param string $password
     */
    public function specifyPassword($password);

    /**
     * @param string $localeCode
     */
    public function specifyLocale($localeCode);
}
