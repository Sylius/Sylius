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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ProcessLowestPriceOnCheckingPeriodChangeObserver implements EntityObserverInterface
{
    public function __construct(
        private ProductLowestPriceBeforeDiscountProcessorInterface $productLowestPriceBeforeDiscountProcessor,
        private RepositoryInterface $channelPricingRepository,
        private int $batchSize,
    ) {
    }

    public function onChange(object $entity): void
    {
        Assert::isInstanceOf($entity, ChannelInterface::class);

        $limit = $this->batchSize;
        $offset = 0;

        do {
            /** @var ChannelPricingInterface[] $channelPricings */
            $channelPricings = $this->channelPricingRepository->findBy(
                ['channelCode' => $entity->getCode()],
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

    public function supports(object $entity): bool
    {
        return $entity instanceof ChannelInterface;
    }

    public function observedFields(): array
    {
        return ['lowestPriceForDiscountedProductsCheckingPeriod'];
    }
}
