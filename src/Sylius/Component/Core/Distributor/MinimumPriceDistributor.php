<?php

namespace Sylius\Component\Core\Distributor;

class MinimumPriceDistributor implements MinimumPriceDistributorInterface
{
    private ProportionalIntegerDistributorInterface $proportionalIntegerDistributor;

    public function __construct(ProportionalIntegerDistributorInterface $proportionalIntegerDistributor)
    {
        $this->proportionalIntegerDistributor = $proportionalIntegerDistributor;
    }

    public function distributeWithMinimumPrice(int $promotionAmount, array $itemTotals, array $minimumPrices, $distributed = [], $toDistribute = []): array
    {
//        array_multisort($minimumPrices, SORT_DESC, $itemTotals);

        $splitPromotion = $this->proportionalIntegerDistributor->distribute($itemTotals, $promotionAmount);

        $exceedsMinimumPrice = false;
//        $promotionAmountLeft = 0;
        $minimumPrices2 = [];
        $newDiscounts = [];
        foreach ($splitPromotion as $key => $splitPromotionAmount) {
            if ($itemTotals[$key] + $splitPromotionAmount <= $minimumPrices[$key] && $minimumPrices[$key] > 0 && $exceedsMinimumPrice === false) {
                $availableAmount = $itemTotals[$key] - $minimumPrices[$key];
                $splitPromotion[$key] = -$availableAmount;
                $promotionAmount += $availableAmount;
//                $promotionAmountLeft += ($splitPromotionAmount + $availableAmount);
                $distributed[] = $splitPromotion[$key];
                $exceedsMinimumPrice = true;
            } else {
                $toDistribute[] = $itemTotals[$key];
                $minimumPrices2[] = $minimumPrices[$key];
//                $promotionAmountLeft += $splitPromotionAmount;
            }
        }

        if ($exceedsMinimumPrice === true && array_sum($toDistribute) > 0) {
            return array_merge($distributed, $this->distributeWithMinimumPrice($promotionAmount, $toDistribute, $minimumPrices2));
//            return $this->merge($distributed, $this->distributeWithMinimumPrice($promotionAmount, $toDistribute, $minimumPrices2), $newDiscounts);

        }

        return $splitPromotion;
    }


//    private function merge(array $distributed, array $splitPromotion, array $newDiscounts): array
//    {
////        $temp = array_merge($distributed, $splitPromotion);
//            $temp = $splitPromotion;
////        $retArr = [];
////
////        for ($x = 0; $x < sizeof($temp) + sizeof($newDiscounts); $x++) {
////            $value = $temp[$x];
////
////            if (isset($newDiscounts[$x])) {
////
////            }
////        }
////        return $temp;
//
//        foreach ($newDiscounts as $key => $newDiscount) {
//            array_splice($temp, $key, 0, $newDiscount);
//        }
//
//        return $temp;
//    }
}
