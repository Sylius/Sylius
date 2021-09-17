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

/** @experimental */
class SendResetPasswordEmail
{
    public string $email;

    public string $channelCode;

    public string $localeCode;

    public function __construct(
        string $email,
        string $channelCode,
        string $localeCode
    ) {
        $this->email = $email;
        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }

    public function localeCode(): string
    {
        return $this->localeCode;
    }
}
