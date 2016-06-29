<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Promotion\Action;

use Sylius\Core\Model\AdjustmentInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\OrderItemInterface;
use Sylius\Core\Model\OrderItemUnitInterface;
use Sylius\Originator\Originator\OriginatorInterface;
use Sylius\Promotion\Action\PromotionActionInterface;
use Sylius\Promotion\Model\PromotionInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
abstract class UnitDiscountAction implements PromotionActionInterface
{
    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @var OriginatorInterface
     */
    protected $originator;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param OriginatorInterface $originator
     */
    public function __construct(FactoryInterface $adjustmentFactory, OriginatorInterface $originator)
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->originator = $originator;
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
            if ($promotion === $this->originator->getOrigin($adjustment)) {
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
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());

        $this->originator->setOrigin($adjustment, $promotion);

        return $adjustment;
    }
}
