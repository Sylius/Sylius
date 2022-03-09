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

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\IriToIdentifierConversionAwareInterface;
use Sylius\Bundle\ApiBundle\Command\LocaleCodeAwareInterface;
use Sylius\Bundle\ApiBundle\Command\ShopUserIdAwareInterface;

/** @experimental */
class ResendVerificationEmail implements ShopUserIdAwareInterface, ChannelCodeAwareInterface, LocaleCodeAwareInterface, IriToIdentifierConversionAwareInterface
{
    /** @var string|int|null */
    public $shopUserId = null;

    /**
     * @psalm-immutable
     * @var string|null
     */
    public $channelCode;

    /**
     * @psalm-immutable
     * @var string|null
     */
    public $localeCode;

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

    public function getShopUserId()
    {
        return $this->shopUserId;
    }

    public function setShopUserId($shopUserId): void
    {
        $this->shopUserId = $shopUserId;
    }
}
