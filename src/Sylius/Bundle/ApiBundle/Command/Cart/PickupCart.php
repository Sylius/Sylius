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

use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;

/**
 * @experimental
 * @psalm-immutable
 */
class PickupCart implements ChannelCodeAwareInterface
{
    /** @var string|null */
    public $tokenValue;

    /** @var string|null */
    public $channelCode;

    public function __construct(?string $tokenValue = null)
    {
        $this->tokenValue = $tokenValue;
    }

    public function getChannelCode(): ?string
    {
        return $this->channelCode;
    }

    public function setChannelCode(?string $channelCode): void
    {
        $this->channelCode = $channelCode;
    }
}
