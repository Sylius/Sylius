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

namespace Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver;

use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver implements EntityObserverInterface
{
    private array $configsCurrentlyProcessed = [];

    public function __construct(
        private ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        private RepositoryInterface $channelPricingRepository,
        private ChannelRepositoryInterface $channelRepository,
        private int $batchSize,
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

        $this->processPeriodUpdate($channel);

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

    private function processPeriodUpdate(ChannelInterface $channel): void
    {
        $limit = $this->batchSize;
        $offset = 0;

        do {
            /** @var ChannelPricingInterface[] $channelPricings */
            $channelPricings = $this->channelPricingRepository->findBy(
                ['channelCode' => $channel->getCode()],
                ['id' => 'ASC'],
                $limit,
                $offset,
            );

            foreach ($channelPricings as $channelPricing) {
                $this->productLowestPriceBeforeDiscountProcessor->process($channelPricing);
            }

            $offset += $limit;
        } while ([] !== $channelPricings);
    }
}
