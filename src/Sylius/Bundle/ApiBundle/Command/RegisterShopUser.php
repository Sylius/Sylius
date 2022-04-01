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
     * @psalm-immutable
     *
     * @var string
     */
    public $firstName;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $lastName;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $email;

    /**
     * @psalm-immutable
     *
     * @var string
     */
    public $password;

    /**
     * @psalm-immutable
     *
     * @var string|null
     */
    public $phoneNumber;

    /**
     * @psalm-immutable
     *
     * @var bool
     */
    public $subscribedToNewsletter;

    /** @var string|null */
    public $channelCode;

    /** @var string|null */
    public $localeCode;

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
