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

/**
 * @experimental
 *
 * @psalm-immutable
 */
class VerifyCustomerAccount implements ChannelCodeAwareInterface, LocaleCodeAwareInterface
{
    public function __construct(
        public string $token,
        private ?string $localeCode = null,
        private ?string $channelCode = null,
    ) {
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        /** @psalm-suppress InaccessibleProperty */
        $this->channelCode = $channelCode;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(?string $localeCode): void
    {
        /** @psalm-suppress InaccessibleProperty */
        $this->localeCode = $localeCode;
    }
}
