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

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UnitsPromotionAdjustmentsApplicator implements UnitsPromotionAdjustmentsApplicatorInterface
{
    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var IntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var OriginatorInterface
     */
    private $originator;

    /**
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param IntegerDistributorInterface $distributor
     * @param OriginatorInterface $originator
     */
    public function __construct(
        AdjustmentFactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $distributor,
        OriginatorInterface $originator
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->distributor = $distributor;
        $this->originator = $originator;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order, PromotionInterface $promotion, array $adjustmentsAmounts)
    {
        Assert::eq($order->countItems(), count($adjustmentsAmounts));

        $i = 0;
        foreach ($order->getItems() as $item) {
            if (0 === $adjustmentsAmounts[$i]) {
                continue;
            }

            $this->applyAdjustmentsOnItemUnits($item, $promotion, $adjustmentsAmounts[$i]);
            $i++;
        }
    }

    /**
     * @param OrderItemInterface $item
     * @param PromotionInterface $promotion
     * @param int $itemPromotionAmount
     */
    private function applyAdjustmentsOnItemUnits(OrderItemInterface $item, PromotionInterface $promotion, $itemPromotionAmount)
    {
        $splitPromotionAmount = $this->distributor->distribute($itemPromotionAmount, $item->getQuantity());

        $i = 0;
        foreach ($item->getUnits() as $unit) {
            if (0 === $splitPromotionAmount[$i]) {
                continue;
            }

            $this->addAdjustment($promotion, $unit, $splitPromotionAmount[$i]);
            $i++;
        }
    }

    /**
     * @param PromotionInterface $promotion
     * @param OrderItemUnitInterface $unit
     * @param int $amount
     */
    private function addAdjustment(PromotionInterface $promotion, OrderItemUnitInterface $unit, $amount)
    {
        $adjustment = $this->adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, $promotion->getName(), $amount)
        ;

        $this->originator->setOrigin($adjustment, $promotion);

        $unit->addAdjustment($adjustment);
    }
}
