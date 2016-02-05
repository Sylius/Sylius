<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      05/02/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\Modifier;

use Doctrine\ORM\EntityManager;
use Sylius\Component\Order\Model\Adjustment;
use Sylius\Component\Order\Factory\AdjustmentFactory;
use Sylius\Component\Order\Model\Order;
use Sylius\Component\Order\Model\OrderItemUnit;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class AdjustmentManager
{
    /**
     * @var AdjustmentFactory
     */
    private $adjustmentFactory;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param AdjustmentFactory $adjustmentFactory
     * @param EntityManager     $entityManager
     */
    public function __construct(AdjustmentFactory $adjustmentFactory, EntityManager $entityManager)
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->entityManager     = $entityManager;
    }

    /**
     * @param OrderItemUnit $unit
     * @param string        $descriptionSuffix
     */
    public function buildRefundAdjustmentsForUnit(OrderItemUnit $unit, $descriptionSuffix = 'Return')
    {
        if (count($unit->getRefundAdjustments())) {
            // If already has some, do nothing.
            return;
        }

        foreach ($unit->getAdjustments() as $adjustment) {
            $refundAdjustment = clone $adjustment;
            $refundAdjustment->setRefund(true);
            $refundAdjustment->setAmount($refundAdjustment->getAmount() * -1);
            $refundAdjustment->setDescription($refundAdjustment->getDescription() . ' ' . $descriptionSuffix);
            $refundAdjustment->setAdjustable($unit);

            $this->entityManager->persist($refundAdjustment);
        }

        // Adjustment for initial unit price. Must be done AFTER above loop
        $adjustment = $this->adjustmentFactory->createUnitRefund($unit, $descriptionSuffix);

        $this->entityManager->persist($adjustment);
    }

    /**
     * @param Order $order
     */
    public function buildShippingRefundAdjustment(Order $order)
    {
        if (count($order->getRefundAdjustments())) {
            // If already has some, do nothing.
            return;
        }

        foreach ($order->getAdjustments(Adjustment::SHIPPING_ADJUSTMENT) as $adjustment) {
            $refundAdjustment = clone $adjustment;
            $refundAdjustment->setRefund(true);
            $refundAdjustment->setAmount($refundAdjustment->getAmount() * -1);
            $refundAdjustment->setAdjustable($order);

            $this->entityManager->persist($refundAdjustment);
        }
    }
}