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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LoggedInCustomerEmailAwareInterface;

class PickupCart implements ChannelCodeAwareInterface, LoggedInCustomerEmailAwareInterface, LocaleCodeAwareInterface
{
    public function __construct(
        protected ?string $tokenValue = null,
        protected ?string $channelCode = null,
        protected ?string $email = null,
        protected ?string $localeCode = null,
    ) {
    }

    public function getTokenValue(): ?string
    {
        return $this->tokenValue;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }
}
