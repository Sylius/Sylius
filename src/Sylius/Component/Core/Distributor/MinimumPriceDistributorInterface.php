<?php


namespace Sylius\Component\Core\Distributor;


interface MinimumPriceDistributorInterface
{
    public function distributeWithMinimumPrice(int $promotionAmount, array $itemTotals, array $minimumPrices, $distributed = [], $toDistribute = []): array;
}
