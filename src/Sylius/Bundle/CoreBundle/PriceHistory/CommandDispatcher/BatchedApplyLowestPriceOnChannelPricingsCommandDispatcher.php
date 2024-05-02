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

namespace Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\CoreBundle\PriceHistory\Command\ApplyLowestPriceOnChannelPricings;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher implements ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface
{
    public function __construct(
        private RepositoryInterface $channelPricingRepository,
        private MessageBusInterface $commandBus,
        private int $batchSize,
    ) {
    }

    public function applyWithinChannel(ChannelInterface $channel): void
    {
        $limit = $this->batchSize;
        $offset = 0;

        while ([] !== ($channelPricingsIds = $this->getIdsBatch($channel, $limit, $offset))) {
            $this->commandBus->dispatch(new ApplyLowestPriceOnChannelPricings($channelPricingsIds));

            $offset += $limit;
        }
    }

    private function getIdsBatch(ChannelInterface $channel, int $limit, int $offset): array
    {
        /** @var ChannelPricingInterface[] $channelPricings */
        $channelPricings = $this->channelPricingRepository->findBy(
            ['channelCode' => $channel->getCode()],
            ['id' => 'ASC'],
            $limit,
            $offset,
        );

        return (new ArrayCollection($channelPricings))
            ->map(fn (ChannelPricingInterface $channelPricing): mixed => $channelPricing->getId())
            ->getValues()
        ;
    }
}
