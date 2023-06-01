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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of ChannelPricingLogEntryInterface
 *
 * @extends FactoryInterface<T>
 */
interface ChannelPricingLogEntryFactoryInterface extends FactoryInterface
{
    public function create(
        ChannelPricingInterface $channelPricing,
        \DateTimeInterface $loggedAt,
        int $price,
        ?int $originalPrice = null,
    ): ChannelPricingLogEntryInterface;
}
