<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
abstract class UnitDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(FactoryInterface $adjustmentFactory)
    {
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        foreach ($subject->getItems() as $item) {
            $this->removeUnitsAdjustment($item, $promotion);
        }
    }

    /**
     * @param OrderItemInterface $item
     * @param PromotionInterface $promotion
     */
    protected function removeUnitsAdjustment(OrderItemInterface $item, PromotionInterface $promotion)
    {
        foreach ($item->getUnits() as $unit) {
            $this->removeUnitOrderItemAdjustments($unit, $promotion);
        }
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param PromotionInterface $promotion
     */
    protected function removeUnitOrderItemAdjustments(OrderItemUnitInterface $unit, PromotionInterface $promotion)
    {
        foreach ($unit->getAdjustments(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param int $amount
     * @param PromotionInterface $promotion
     */
    protected function addAdjustmentToUnit(OrderItemUnitInterface $unit, $amount, PromotionInterface $promotion)
    {
        $adjustment = $this->createAdjustment($promotion, AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT);
        $adjustment->setAmount(-$amount);

        $unit->addAdjustment($adjustment);
    }

    /**
     * @param PromotionInterface $promotion
     * @param string $type
     *
     * @return AdjustmentInterface
     */
    protected function createAdjustment(PromotionInterface $promotion, $type = AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
    {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());
        $adjustment->setOriginCode($promotion->getCode());

        return $adjustment;
    }
}
