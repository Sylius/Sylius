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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\CustomerEmailAwareInterface;
use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;

/** @experimental */
class PickupCart implements ChannelCodeAwareInterface, CustomerEmailAwareInterface
{
    /** @psalm-immutable */
    public ?string $tokenValue;

    /** @psalm-immutable */
    public ?string $localeCode;

    public ?string $channelCode = null;

    public ?string $email = null;

    public function __construct(?string $tokenValue = null, ?string $localeCode = null)
    {
        $this->tokenValue = $tokenValue;
        $this->localeCode = $localeCode;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
}
