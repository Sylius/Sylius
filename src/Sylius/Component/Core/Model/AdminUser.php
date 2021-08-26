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

namespace Sylius\Component\Core\Model;

use Sylius\Component\User\Model\User;

class AdminUser extends User implements AdminUserInterface
{
    protected ?string $firstName = null;

    protected ?string $lastName = null;

    protected ?string $localeCode = null;

    protected ?ImageInterface $avatar = null;

    public function __construct()
    {
        parent::__construct();

        $this->roles = [AdminUserInterface::DEFAULT_ADMIN_ROLE];
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $code): void
    {
        $this->localeCode = $code;
    }

    public function getImage(): ?ImageInterface
    {
        return $this->avatar;
    }

    public function setImage(?ImageInterface $image): void
    {
        $image->setOwner($this);
        $this->avatar = $image;
    }

    public function getAvatar(): ?ImageInterface
    {
        return $this->getImage();
    }

    public function setAvatar(?ImageInterface $avatar): void
    {
        $this->setImage($avatar);
    }
}
