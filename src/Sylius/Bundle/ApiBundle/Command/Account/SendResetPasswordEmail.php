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

class SendResetPasswordEmail
{
    /** @var string */
    public $email;

    /** @var string */
    public $channelCode;

    /** @var string */
    public $localeCode;

    public function __construct(
        string $email,
        string $channelCode,
        string $localeCode,
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
