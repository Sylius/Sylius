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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\AdjustmentInterface as OrderAdjustmentInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ShippingPercentageDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    public const TYPE = 'shipping_percentage_discount';

    public function __construct(private FactoryInterface $adjustmentFactory)
    {
    }

    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): bool
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        if (!isset($configuration['percentage'])) {
            return false;
        }

        if (!$subject->hasShipments()) {
            return false;
        }

        $result = false;
        foreach ($subject->getShipments() as $shipment) {
            $maxDiscount = $shipment->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT) + $shipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
            Assert::integer($maxDiscount);
            if ($maxDiscount < 0) {
                continue;
            }

            $adjustmentAmount = (int) round($shipment->getAdjustmentsTotal(AdjustmentInterface::SHIPPING_ADJUSTMENT) * $configuration['percentage']);
            if (0 === $adjustmentAmount) {
                continue;
            }

            if ($maxDiscount < $adjustmentAmount) {
                $adjustmentAmount = $maxDiscount;
            }

            $adjustment = $this->createAdjustment($promotion);
            $adjustment->setAmount(-$adjustmentAmount);
            $shipment->addAdjustment($adjustment);
            $result = true;
        }

        return $result;
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion): void
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        if (!$subject->hasShipments()) {
            return;
        }

        foreach ($subject->getAdjustments(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $subject->removeAdjustment($adjustment);
            }
        }

        foreach ($subject->getShipments() as $shipment) {
            $this->removePromotionFromShipment($promotion, $shipment);
        }
    }

    private function createAdjustment(
        PromotionInterface $promotion,
        string $type = AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT,
    ): OrderAdjustmentInterface {
        /** @var OrderAdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType($type);
        $adjustment->setLabel($promotion->getName());
        $adjustment->setOriginCode($promotion->getCode());

        return $adjustment;
    }

    private function removePromotionFromShipment(PromotionInterface $promotion, ShipmentInterface $shipment): void
    {
        foreach ($shipment->getAdjustments(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT) as $adjustment) {
            if ($promotion->getCode() === $adjustment->getOriginCode()) {
                $shipment->removeAdjustment($adjustment);
            }
        }
    }
}
