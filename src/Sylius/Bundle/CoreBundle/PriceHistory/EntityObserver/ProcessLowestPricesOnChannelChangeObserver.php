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

namespace Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver;

use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Contracts\Service\ResetInterface;
use Webmozart\Assert\Assert;

final class ProcessLowestPricesOnChannelChangeObserver implements EntityObserverInterface, ResetInterface
{
    /** @var array<mixed> */
    private array $channelsCurrentlyProcessed = [];

    public function __construct(private ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher)
    {
    }

    public function onChange(object $entity): void
    {
        Assert::isInstanceOf($entity, ChannelInterface::class);

        $this->channelsCurrentlyProcessed = [(string) $entity->getCode() => true];

        try {
            $this->commandDispatcher->applyWithinChannel($entity);
        } finally {
            unset($this->channelsCurrentlyProcessed[(string) $entity->getCode()]);
        }
    }

    public function supports(object $entity): bool
    {
        return
            $entity instanceof ChannelInterface &&
            !isset($this->channelsCurrentlyProcessed[$entity->getCode()]) &&
            $this->hasNewPriceHistoryConfig($entity)
        ;
    }

    public function observedFields(): array
    {
        return ['channelPriceHistoryConfig'];
    }

    private function hasNewPriceHistoryConfig(ChannelInterface $channel): bool
    {
        return
            (null !== $config = $channel->getChannelPriceHistoryConfig()) &&
            null === $config->getId()
        ;
    }

    public function reset(): void
    {
        $this->channelsCurrentlyProcessed = [];
    }
}
