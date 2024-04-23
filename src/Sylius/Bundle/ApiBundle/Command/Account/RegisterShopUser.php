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

namespace Sylius\Bundle\ApiBundle\Command\Account;

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;

/** @experimental */
class RegisterShopUser implements ChannelCodeAwareInterface, LocaleCodeAwareInterface
{
    public function __construct(
        protected string $firstName,
        protected string $lastName,
        protected string $email,
        protected string $password,
        protected ?string $channelCode,
        protected ?string $localeCode,
        protected bool $subscribedToNewsletter = false,
    ) {
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function isSubscribedToNewsletter(): bool
    {
        return $this->subscribedToNewsletter;
    }
}
