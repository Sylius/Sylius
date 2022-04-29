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

/** @experimental */
class ResendVerificationEmail implements ChannelCodeAwareInterface, LocaleCodeAwareInterface
{
    /** @var string */
    public $email;

    /**
     * @psalm-immutable
     *
     * @var string|null
     */
    public $channelCode;

    /**
     * @psalm-immutable
     *
     * @var string|null
     */
    public $localeCode;

    public function __construct(string $email)
    {
        $this->email = $email;
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
