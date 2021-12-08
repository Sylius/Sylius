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

namespace Sylius\Component\Core\Distributor;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class MinimumPriceDistributor implements MinimumPriceDistributorInterface
{
    private ProportionalIntegerDistributorInterface $proportionalIntegerDistributor;

    public function __construct(ProportionalIntegerDistributorInterface $proportionalIntegerDistributor)
    {
        $this->proportionalIntegerDistributor = $proportionalIntegerDistributor;
    }

    public function distribute(array $orderItems, int $amount, ChannelInterface $channel): array
    {
        Assert::allIsInstanceOf($orderItems, OrderItemInterface::class);

        $orderItemsToProcess = [];
        foreach ($orderItems as $index => $orderItem) {
            /** @var ProductVariantInterface $variant */
            $variant = $orderItem->getVariant();

            $minimumPrice = $variant->getChannelPricingForChannel($channel)->getMinimumPrice();

            $minimumPrice *= $orderItem->getQuantity();

            $orderItemsToProcess['order-item-' . $index] = [
                'orderItem' => $orderItem,
                'minimumPrice' => $minimumPrice,
            ];
        }

        return array_values(array_map(
            function (array $processedOrderItem): int { return $processedOrderItem['promotion']; },
            $this->processDistributionWithMinimumPrice($orderItemsToProcess, $amount, $channel)
        ));
    }

    private function processDistributionWithMinimumPrice(array $orderItems, int $amount, $channel): array
    {
        $totals = array_values(array_map(function (array $orderItemData): int {
            return $orderItemData['orderItem']->getTotal();
        }, $orderItems));

        $promotionsToDistribute = array_combine(
            array_keys($orderItems),
            $this->proportionalIntegerDistributor->distribute($totals, $amount)
        );

        foreach ($promotionsToDistribute as $index => $promotion) {
            $orderItems[$index]['promotion'] = $promotion;
        }

        $leftAmount = 0;
        $distributableItems = [];
        foreach ($orderItems as $index => $distribution) {
            /** @var OrderItemInterface $orderItem */
            $orderItem = $distribution['orderItem'];
            $minimumPriceAdjustedByCurrentDiscount = $distribution['minimumPrice'];
            $proposedPromotion = $distribution['promotion'];

            if ($this->exceedsOrderItemMinimumPrice($orderItem->getTotal(), $minimumPriceAdjustedByCurrentDiscount, $proposedPromotion)) {
                $leftAmount += ($orderItem->getTotal() + $proposedPromotion) - ($minimumPriceAdjustedByCurrentDiscount);
                $orderItems[$index]['promotion'] = $minimumPriceAdjustedByCurrentDiscount - $orderItem->getTotal();

                continue;
            }

            $distributableItems[$index] = [
                'orderItem' => $orderItem,
                'minimumPrice' => $distribution['minimumPrice'] - $proposedPromotion,
            ];
        }

        if ($leftAmount === 0 || empty($distributableItems)) {
            return $orderItems;
        }

        $nestedDistributions = $this->processDistributionWithMinimumPrice($distributableItems, $leftAmount, $channel);

        foreach ($nestedDistributions as $index => $distribution) {
            $orderItems[$index]['promotion'] += $distribution['promotion'];
        }

        return $orderItems;
    }

    private function exceedsOrderItemMinimumPrice(
        int $orderItemTotal,
        int $minimumPriceAdjustedByCurrentDiscount,
        int $proposedPromotion
    ): bool {
        return $minimumPriceAdjustedByCurrentDiscount >= ($orderItemTotal + $proposedPromotion);
    }
}
