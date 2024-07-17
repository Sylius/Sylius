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
class VerifyShopUser implements ChannelCodeAwareInterface, LocaleCodeAwareInterface
{
    public function __construct(
        protected string $token,
        protected string $channelCode,
        protected string $localeCode,
    ) {
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }

    public function getLocaleCode(): string
    {
        return $this->localeCode;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
