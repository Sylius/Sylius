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

namespace Sylius\Bundle\ApiBundle\Command;

class SendContactRequest implements ChannelCodeAwareInterface, LocaleCodeAwareInterface, LoggedInCustomerEmailAwareInterface
{
    public function __construct(
        protected ?string $channelCode,
        protected ?string $localeCode,
        protected ?string $email = null,
        protected ?string $message = null,
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
