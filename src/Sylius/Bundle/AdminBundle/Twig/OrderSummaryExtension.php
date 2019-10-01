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

namespace Sylius\Bundle\AdminBundle\Twig;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OrderSummaryExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'sylius_order_summary_unit_order_discount', [$this, 'getUnitOrderDiscount']
            ),
            new TwigFunction(
                'sylius_order_summary_discounted_unit_price_and_quantity', [$this, 'getDiscountedUnitPriceAndQuantity']
            ),
            new TwigFunction(
                'sylius_order_summary_subtotal', [$this, 'getSubtotal']
            ),
        ];
    }

    public function getUnitOrderDiscount(OrderItemInterface $orderItem): array
    {
        $units = $orderItem->getUnits();

        $unitsTypes = [];

        for ($unitCounter = 0 ; $unitCounter < $units->count() ; $unitCounter++) {
            $unitPrice = $this->getUnitOrderDiscountByIndex($unitCounter, $orderItem);

            if (array_key_exists($unitPrice, $unitsTypes)) {
                $unitsTypes[$unitPrice] = $unitsTypes[$unitPrice] + 1;
                continue;
            }
            $unitsTypes[$unitPrice] = 1;
        }

        return $unitsTypes;
    }

    public function getDiscountedUnitPriceAndQuantity(OrderItemInterface $orderItem): array
    {
        $units = $orderItem->getUnits();

        $unitsTypes = [];

        for ($unitCounter = 0 ; $unitCounter < $units->count() ; $unitCounter++) {
            $unitPrice = $this->getFullDiscountedUnitPriceByIndex($unitCounter, $orderItem);

            if (array_key_exists($unitPrice, $unitsTypes)) {
                $unitsTypes[$unitPrice] = $unitsTypes[$unitPrice] + 1;
                continue;
            }
            $unitsTypes[$unitPrice] = 1;
        }

        return $unitsTypes;
    }

    public function getSubtotal(OrderItemInterface $orderItem): int
    {
        $units = $orderItem->getUnits();

        $unitPrice = $orderItem->getUnitPrice();

        if ($units->isEmpty()) {
            return $unitPrice * $orderItem->getQuantity();
        }

        /** @var int $adjustmentsTotal */
        $adjustmentsTotal = 0;

        foreach ($units as $unit) {
            /** @var int $adjustmentsTotal */
            $adjustmentsTotal = $adjustmentsTotal +
                $unitPrice +
                $unit->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT) +
                $unit->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ;
        }

        if ($adjustmentsTotal > 0) {
            return $adjustmentsTotal;
        }

        return 0;
    }

    private function getFullDiscountedUnitPriceByIndex(int $index, OrderItemInterface $orderItem): int
    {
        $units = $orderItem->getUnits();

        $unitPrice = $orderItem->getUnitPrice();

        if ($units->isEmpty()) {
            return $unitPrice;
        }

        return
            $unitPrice +
            $units->get($index)->getAdjustmentsTotal(AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT) +
            $units->get($index)->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT)
            ;
    }

    private function getUnitOrderDiscountByIndex(int $index, OrderItemInterface $orderItem): int
    {
        $units = $orderItem->getUnits();

        $unitPrice = $orderItem->getUnitPrice();

        if ($units->isEmpty()) {
            return $unitPrice;
        }

        return $units->get($index)->getAdjustmentsTotal(AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT);
    }
}
