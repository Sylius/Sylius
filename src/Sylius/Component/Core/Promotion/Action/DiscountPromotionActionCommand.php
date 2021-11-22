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

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

abstract class DiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    abstract protected function isConfigurationValid(array $configuration): void;

    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf($subject, OrderInterface::class);

        if (!$this->isSubjectValid($subject)) {
            return;
        }

        foreach ($subject->getItems() as $item) {
            foreach ($item->getUnits() as $unit) {
                $this->removeUnitOrderPromotionAdjustmentsByOrigin($unit, $promotion);
            }
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function isSubjectValid(PromotionSubjectInterface $subject): bool
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf($subject, OrderInterface::class);

        return 0 !== $subject->countItems();
    }

    protected function distributeWithMinimumPrice(array &$splitPromotion, array $itemTotals, array $minimumPrices): void
    {
        $promotionAmountLeft = 0;
        foreach ($splitPromotion as $key => $splitPromotionAmount) {
            $splitPromotion[$key] += $promotionAmountLeft;

            if ($itemTotals[$key] + $splitPromotion[$key] < $minimumPrices[$key]) {
                $availableAmount = $itemTotals[$key] - $minimumPrices[$key];
                $splitPromotion[$key] = -$availableAmount;

                $promotionAmountLeft += ($splitPromotionAmount + $availableAmount);
            }
        }
    }

    private function removeUnitOrderPromotionAdjustmentsByOrigin(
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion
    ): void {
        foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }
}
