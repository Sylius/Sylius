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

/** @immutable */
class SendAccountVerificationEmail
{
    /** @var string */
    public $shopUserEmail;

    /** @var string */
    public $localeCode;

    /** @var string */
    public $channelCode;

    public function __construct(string $shopUserEmail, string $localeCode, string $channelCode)
    {
        $this->shopUserEmail = $shopUserEmail;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;
    }
}
