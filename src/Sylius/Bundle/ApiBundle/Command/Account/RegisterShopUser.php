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

namespace Sylius\Bundle\ApiBundle\Command\Account;

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;

/**
 * @experimental
 */
class RegisterShopUser implements ChannelCodeAwareInterface, LocaleCodeAwareInterface
{
    /**
     * @psalm-immutable
     */
    public string $firstName;

    /**
     * @psalm-immutable
     */
    public string $lastName;

    /**
     * @psalm-immutable
     */
    public string $email;

    /**
     * @psalm-immutable
     */
    public string $password;

    /**
     * @psalm-immutable
     */
    public ?string $phoneNumber;

    /**
     * @psalm-immutable
     */
    public bool $subscribedToNewsletter;

    public ?string $channelCode = null;

    public ?string $localeCode = null;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        ?string $phoneNumber = null,
        bool $subscribedToNewsletter = false
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->phoneNumber = $phoneNumber;
        $this->subscribedToNewsletter = $subscribedToNewsletter;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $localeCode): void
    {
        $this->localeCode = $localeCode;
    }
}
