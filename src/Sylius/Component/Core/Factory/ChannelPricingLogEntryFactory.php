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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @template T of ChannelPricingLogEntryInterface
 *
 * @implements ChannelPricingLogEntryFactoryInterface<T>
 */
final class ChannelPricingLogEntryFactory implements ChannelPricingLogEntryFactoryInterface
{
    public function __construct(private string $className)
    {
        if (!is_a($className, ChannelPricingLogEntryInterface::class, true)) {
            throw new \DomainException(sprintf(
                'This factory requires %s or its descend to be used as resource',
                ChannelPricingLogEntryInterface::class,
            ));
        }
    }

    /**
     * @throws UnsupportedMethodException
     */
    public function createNew(): object
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function create(
        ChannelPricingInterface $channelPricing,
        \DateTimeInterface $loggedAt,
        int $price,
        ?int $originalPrice = null,
    ): ChannelPricingLogEntryInterface {
        /** @var ChannelPricingLogEntryInterface $channelPricingLogEntry */
        $channelPricingLogEntry = new $this->className(
            $channelPricing,
            $loggedAt,
            $price,
            $originalPrice,
        );

        return $channelPricingLogEntry;
    }
}
