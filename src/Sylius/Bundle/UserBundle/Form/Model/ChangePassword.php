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

namespace Sylius\Bundle\UserBundle\Form\Model;

class ChangePassword
{
    /**
     * @var string|null
     */
    private $currentPassword;

    /**
     * @var string|null
     */
    private $newPassword;

    /**
     * @return string|null
     */
    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    /**
     * @param string|null $password
     */
    public function setCurrentPassword(?string $password): void
    {
        $this->currentPassword = $password;
    }

    /**
     * @return string
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    /**
     * @param string $password
     */
    public function setNewPassword(?string $password): void
    {
        $this->newPassword = $password;
    }
}
