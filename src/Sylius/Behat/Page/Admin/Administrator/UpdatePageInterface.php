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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface UpdatePageInterface extends BaseUpdatePageInterface
{
    /**
     * @param string $username
     */
    public function changeUsername($username);

    /**
     * @param string $email
     */
    public function changeEmail($email);

    /**
     * @param string $password
     */
    public function changePassword($password);

    /**
     * @param string $localeCode
     */
    public function changeLocale($localeCode);
}
