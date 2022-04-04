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

namespace Sylius\Component\Core\Taxation\Applicator;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Webmozart\Assert\Assert;

class OrderItemsTaxesApplicator implements OrderTaxesApplicatorInterface
{
    public function __construct(
        private CalculatorInterface $calculator,
        private AdjustmentFactoryInterface $adjustmentFactory,
        private IntegerDistributorInterface $distributor,
        private TaxRateResolverInterface $taxRateResolver,
        private ProportionalIntegerDistributorInterface $proportionalIntegerDistributor
    ) {
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $itemTaxFloatAmounts = [];
        $itemTaxRates = [];

        foreach ($order->getItems() as $index => $item) {
            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                $itemTaxFloatAmounts[$index] = 0;

                continue;
            }

            $itemTaxFloatAmounts[$index] = $this->calculator->calculate($item->getTotal(), $taxRate);
            $itemTaxRates[$index] = $taxRate;
        }

        $itemTaxWholeAmounts = array_map(fn (float $amount) => (int)round($amount), $itemTaxFloatAmounts);
        $itemTotalTaxWholeAmount = (int)round(array_sum($itemTaxFloatAmounts));
        $itemSplitTaxes = $this->proportionalIntegerDistributor->distribute($itemTaxWholeAmounts, $itemTotalTaxWholeAmount);

        foreach ($order->getItems() as $i => $item) {
            $quantity = $item->getQuantity();
            Assert::notSame($quantity, 0, 'Cannot apply tax to order item with 0 quantity.');

            $totalTaxAmount = $itemSplitTaxes[$i];
            $taxRate = $itemTaxRates[$i];

            $unitSplitTaxes = $this->distributor->distribute((float)$totalTaxAmount, $quantity);
            foreach ($item->getUnits() as $j => $unit) {
                if (0 === $unitSplitTaxes[$j]) {
                    continue;
                }

                $this->addAdjustment($unit, $unitSplitTaxes[$j], $taxRate);
            }
        }
    }

    private function addAdjustment(OrderItemUnitInterface $unit, int $taxAmount, TaxRateInterface $taxRate): void
    {
        $unitTaxAdjustment = $this->adjustmentFactory->createWithData(
            AdjustmentInterface::TAX_ADJUSTMENT,
            $taxRate->getLabel(),
            $taxAmount,
            $taxRate->isIncludedInPrice(),
            [
                'taxRateCode' => $taxRate->getCode(),
                'taxRateName' => $taxRate->getName(),
                'taxRateAmount' => $taxRate->getAmount(),
            ],
        );
        $unit->addAdjustment($unitTaxAdjustment);
    }
}
