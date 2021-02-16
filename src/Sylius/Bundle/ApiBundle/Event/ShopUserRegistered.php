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

namespace Sylius\Bundle\ApiBundle\Event;

/** @experimental  */
final class ShopUserRegistered
{
    /** @var string */
    protected $shopUserEmail;

    /** @var string */
    protected $channelCode;

    /** @var string */
    protected $localeCode;

    public function __construct(string $shopUserEmail, string $channelCode, string $localeCode)
    {
        $this->shopUserEmail = $shopUserEmail;
        $this->channelCode = $channelCode;
        $this->localeCode = $localeCode;
    }

    public function getChannelCode(): string
    {
        return $this->channelCode;
    }

    public function getShopUserEmail(): string
    {
        return $this->shopUserEmail;
    }

    public function getLocale(): string
    {
        return $this->localeCode;
    }
}
