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

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as OrderAdjustmentInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;

abstract class UnitDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    public function __construct(protected FactoryInterface $adjustmentFactory)
    {
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        foreach ($subject->getItems() as $item) {
            $this->removeUnitsAdjustment($item, $promotion);
        }
    }

    protected function removeUnitsAdjustment(OrderItemInterface $item, PromotionInterface $promotion): void
    {
        /** @var OrderItemUnitInterface $unit */
        foreach ($item->getUnits() as $unit) {
            $this->removeUnitOrderItemAdjustments($unit, $promotion);
        }
    }

    protected function removeUnitOrderItemAdjustments(OrderItemUnitInterface $unit, PromotionInterface $promotion): void
    {
        foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }

    protected function addAdjustmentToUnit(OrderItemUnitInterface $unit, int $amount, PromotionInterface $promotion): void
    {
        if (!$this->canPromotionBeApplied($unit, $promotion)) {
            return;
        }

        $adjustment = $this->createAdjustment($promotion, AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);

        /** @var OrderItemInterface $orderItem */
        $orderItem = $unit->getOrderItem();

        /** @var ProductVariantInterface $variant */
        $variant = $orderItem->getVariant();

        /** @var OrderInterface $order */
        $order = $orderItem->getOrder();

        $channel = $order->getChannel();

        $minimumPrice = $variant->getChannelPricingForChannel($channel)->getMinimumPrice();

        $adjustment->setAmount($this->calculate($unit->getTotal(), $minimumPrice, -$amount));

        $unit->addAdjustment($adjustment);
    }

    protected function createAdjustment(
        PromotionInterface $promotion,
        string $type = AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
    ): OrderAdjustmentInterface {
        /** @var OrderAdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());
        $adjustment->setOriginCode($promotion->getCode());

        return $adjustment;
    }

    private function calculate(int $unitTotal, ?int $minimumPrice, int $promotionAmount): int
    {
        if ($unitTotal + $promotionAmount <= $minimumPrice) {
            return $minimumPrice - $unitTotal;
        }

        return $promotionAmount;
    }

    private function canPromotionBeApplied(OrderItemUnitInterface $unit, PromotionInterface $promotion): bool
    {
        if ($promotion->getAppliesToDiscounted()) {
            return true;
        }

        /** @var OrderItemInterface $item */
        $item = $unit->getOrderItem();
        $variant = $item->getVariant();
        if ($variant === null) {
            return false;
        }

        /** @var OrderInterface $order */
        $order = $item->getOrder();

        return $variant->getAppliedPromotionsForChannel($order->getChannel())->isEmpty();
    }
}
