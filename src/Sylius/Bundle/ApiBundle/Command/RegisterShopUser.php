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

namespace Sylius\Bundle\ApiBundle\Command;

/**
 * @experimental
 */
class RegisterShopUser implements ChannelCodeAwareInterface, LocaleCodeAwareInterface
{
    /**
     * @var string
     * @psalm-immutable
     */
    public $firstName;

    /**
     * @var string
     * @psalm-immutable
     */
    public $lastName;

    /**
     * @var string
     * @psalm-immutable
     */
    public $email;

    /**
     * @var string
     * @psalm-immutable
     */
    public $password;

    /**
     * @var bool
     * @psalm-immutable
     */
    public $subscribedToNewsletter;

    /**
     * @var string|null
     * @psalm-immutable
     */
    public $phoneNumber;

    /** @var string */
    public $channelCode;

    /** @var string|null */
    public $localeCode;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        bool $subscribedToNewsletter = false,
        ?string $phoneNumber = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->subscribedToNewsletter = $subscribedToNewsletter;
        $this->phoneNumber = $phoneNumber;
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
