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
 * @psalm-immutable
 */
class SendShopUserVerificationEmail
{
    /** @var string */
    protected $shopUserEmail;

    /** @var string */
    protected $localeCode;

    /** @var string */
    protected $channelCode;

    public function __construct(string $shopUserEmail, string $localeCode, string $channelCode)
    {
        $this->shopUserEmail = $shopUserEmail;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;
    }

    public function getShopUserEmail(): string
    {
        return $this->shopUserEmail;
    }

    public function getLocale(): string
    {
        return $this->localeCode;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }
}
