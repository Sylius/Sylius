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
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Webmozart\Assert\Assert;

final class ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver implements EntityObserverInterface
{
    private array $configsCurrentlyProcessed = [];

    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface $commandDispatcher,
    ) {
    }

    public function onChange(object $entity): void
    {
        Assert::isInstanceOf($entity, ChannelPriceHistoryConfigInterface::class);
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneBy(['channelPriceHistoryConfig' => $entity]);
        if (null === $channel) {
            return;
        }

        $this->configsCurrentlyProcessed = [$entity->getId() => true];

        $this->commandDispatcher->applyWithinChannel($channel);

        unset($this->configsCurrentlyProcessed[$entity->getId()]);
    }

    public function supports(object $entity): bool
    {
        return
            $entity instanceof ChannelPriceHistoryConfigInterface &&
            null !== $entity->getId() &&
            !isset($this->configsCurrentlyProcessed[$entity->getId()])
        ;
    }

    public function observedFields(): array
    {
        return ['lowestPriceForDiscountedProductsCheckingPeriod'];
    }
}
