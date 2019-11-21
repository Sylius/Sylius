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

class AdminUser extends User implements AdminUserInterface, AvatarAwareInterface
{
    /** @var string */
    protected $firstName;

    /** @var string */
    protected $lastName;

    /** @var string */
    protected $localeCode;

    /** @var ImageInterface */
    protected $avatar;

    public function __construct()
    {
        parent::__construct();

        $this->roles = [AdminUserInterface::DEFAULT_ADMIN_ROLE];
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleCode(?string $code): void
    {
        $this->localeCode = $code;
    }

    public function getAvatar(): ?ImageInterface
    {
        return $this->avatar;
    }

    public function setAvatar(?ImageInterface $avatar): void
    {
        $avatar->setOwner($this);
        $this->avatar = $avatar;
    }

    public function addAvatar(ImageInterface $avatar): void
    {
        $avatar->setOwner($this);
        $this->setAvatar($avatar);
    }

    public function removeAvatar(ImageInterface $avatar): void
    {
        $avatar->setOwner(null);
        $this->avatar = null;
    }

    public function getAvatarImage(): AvatarImageInterface
    {
        /** @var AvatarAwareInterface $avatar */
        $avatar = $this->getAvatar();

        return $avatar;
    }
}
