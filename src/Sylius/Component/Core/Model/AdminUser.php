<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\User\Model\User;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminUser extends User implements AdminUserInterface, EquatableInterface
{
    /** @var string|null */
    protected $firstName;

    /** @var string|null */
    protected $lastName;

    /** @var string|null */
    protected $localeCode;

    /** @var ImageInterface|null */
    protected $avatar;

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
        $image?->setOwner($this);
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

    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof AdminUserInterface && $this->isEnabled() === $user->isEnabled();
    }
}
