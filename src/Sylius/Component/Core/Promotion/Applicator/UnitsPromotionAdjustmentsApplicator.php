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

namespace Sylius\Component\Core\Promotion\Applicator;

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class UnitsPromotionAdjustmentsApplicator implements UnitsPromotionAdjustmentsApplicatorInterface
{
    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;

    /** @var IntegerDistributorInterface */
    private $distributor;

    public function __construct(
        AdjustmentFactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $distributor
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->distributor = $distributor;
    }

    /**
     * {@inheritdoc}
     *
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

            $this->applyAdjustmentsOnItemUnits($item, $promotion, $adjustmentAmount);
        }
    }

    private function applyAdjustmentsOnItemUnits(
        OrderItemInterface $item,
        PromotionInterface $promotion,
        int $itemPromotionAmount
    ): void {
        $splitPromotionAmount = $this->distributor->distribute($itemPromotionAmount, $item->getQuantity());

        $i = 0;
        foreach ($item->getUnits() as $unit) {
            $promotionAmount = $splitPromotionAmount[$i++];
            if (0 === $promotionAmount) {
                continue;
            }

            $this->addAdjustment($promotion, $unit, $promotionAmount);
        }
    }

    private function addAdjustment(PromotionInterface $promotion, OrderItemUnitInterface $unit, int $amount): void
    {
        $adjustment = $this->adjustmentFactory
            ->createWithData(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, $promotion->getName(), $amount)
        ;
        $adjustment->setOriginCode($promotion->getCode());

        $unit->addAdjustment($adjustment);
    }
}
