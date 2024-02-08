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

namespace Sylius\Component\Core\Promotion\Applicator;

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class UnitsPromotionAdjustmentsApplicator implements UnitsPromotionAdjustmentsApplicatorInterface
{
    public function __construct(private AdjustmentFactoryInterface $adjustmentFactory, private IntegerDistributorInterface $distributor)
    {
    }

    /**
     * @throws UnsupportedTypeException
     */
    public function apply(OrderInterface $order, PromotionInterface $promotion, array $adjustmentsAmounts): void
    {
        Assert::eq($order->countItems(), count($adjustmentsAmounts));

        $i = 0;
        foreach ($order->getItems() as $item) {
            $adjustmentAmount = $adjustmentsAmounts[$i++];
            if (0 === $adjustmentAmount) {
                continue;
            }

            $this->applyAdjustmentsOnItemUnits($item, $promotion, $adjustmentAmount, $order->getChannel());
        }
    }

    private function applyAdjustmentsOnItemUnits(
        OrderItemInterface $item,
        PromotionInterface $promotion,
        int $itemPromotionAmount,
        ChannelInterface $channel,
    ): void {
        $splitPromotionAmount = (float) $itemPromotionAmount / $item->getQuantity();
        if (0.0 === $splitPromotionAmount) {
            return;
        }

        $variantMinimumPrice = $item->getVariant()->getChannelPricingForChannel($channel)?->getMinimumPrice() ?? 00;

        foreach ($item->getUnits() as $unit) {
            $this->addAdjustment(
                $promotion,
                $unit,
                $this->calculate($unit->getTotal(), $variantMinimumPrice, $splitPromotionAmount),
            );
        }
    }

    private function addAdjustment(PromotionInterface $promotion, OrderItemUnitInterface $unit, float $amount): void
    {
        $adjustment = $this->adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, $promotion->getName(), $amount)
        ;
        $adjustment->setOriginCode($promotion->getCode());

        $unit->addAdjustment($adjustment);
    }

    private function calculate(float $itemTotal, int $minimumPrice, float $promotionAmount): float
    {
        if ($itemTotal + $promotionAmount <= $minimumPrice) {
            return $minimumPrice - $itemTotal;
        }

        return $promotionAmount;
    }
}
