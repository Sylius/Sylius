<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Applicator;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @author Gorka Laucirica <gorka.lauzirika@gmail.com>
 */
final class OrderItemPromotionAdjustmentsApplicator implements OrderItemPromotionAdjustmentsApplicatorInterface
{
    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(AdjustmentFactoryInterface $adjustmentFactory) {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    public function apply(OrderItemInterface $orderItem, PromotionInterface $promotion, $amount)
    {
        foreach ($orderItem->getUnits() as $unit) {
            $this->addAdjustmentToUnit(
                $unit,
                $promotion,
                min($unit->getTotal(), $amount)
            );
        }
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param PromotionInterface $promotion
     * @param int $amount
     */
    public function addAdjustmentToUnit(OrderItemUnitInterface $unit, PromotionInterface $promotion, $amount)
    {
        $adjustment = $this->createAdjustment($promotion);
        $adjustment->setAmount(-$amount);

        $unit->addAdjustment($adjustment);
    }

    /**
     * @param PromotionInterface $promotion
     *
     * @return AdjustmentInterface
     */
    private function createAdjustment(PromotionInterface $promotion) {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $adjustment->setLabel($promotion->getName());
        $adjustment->setOriginCode($promotion->getCode());

        return $adjustment;
    }
}
