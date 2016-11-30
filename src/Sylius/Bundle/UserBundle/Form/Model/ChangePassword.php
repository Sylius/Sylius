<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\Model;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ChangePassword
{
    /**
     * @var string
     */
    private $currentPassword;

    /**
     * @var string
     */
    private $newPassword;

    /**
     * @return string
     */
    public function getCurrentPassword()
    {
        return $this->currentPassword;
    }

    /**
     * @param string $password
     */
    public function setCurrentPassword($password)
    {
        $this->currentPassword = $password;
    }

    /**
     * @return string
     */
    public function getNewPassword()
    {
        return $this->newPassword;
    }

    /**
     * @param string $password
     */
    public function setNewPassword($password)
    {
        $this->newPassword = $password;
    }
}
