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
use Sylius\Component\Core\Distributor\ProportionalIntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

class OrderItemUnitsTaxesApplicator implements OrderTaxesApplicatorInterface
{
    public function __construct(
        private CalculatorInterface $calculator,
        private AdjustmentFactoryInterface $adjustmentFactory,
        private TaxRateResolverInterface $taxRateResolver,
        private ?ProportionalIntegerDistributorInterface $proportionalIntegerDistributor = null,
    ) {
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        if ($this->proportionalIntegerDistributor === null) {
            $this->applyWithoutDistributionToUnits($order, $zone);

            return;
        }

        foreach ($order->getItems() as $item) {
            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                continue;
            }

            $units = $item->getUnits()->getValues();
            $unitTaxFloatAmounts = [];
            $unitTaxRates = [];

            foreach ($units as $index => $unit) {
                $unitTaxFloatAmounts[$index] = $this->calculator->calculate($unit->getTotal(), $taxRate);
                $unitTaxRates[$index] = $taxRate;
            }

            $unitTaxWholeAmounts = array_map(fn (float $amount) => (int) round($amount), $unitTaxFloatAmounts);
            $unitTotalTaxWholeAmount = (int) round(array_sum($unitTaxFloatAmounts));
            $unitSplitTaxes = $this->proportionalIntegerDistributor->distribute($unitTaxWholeAmounts, $unitTotalTaxWholeAmount);

            foreach ($units as $index => $unit) {
                if (0 === $unitSplitTaxes[$index] || !isset($unitTaxRates[$index])) {
                    continue;
                }

                $this->addAdjustment($unit, $unitSplitTaxes[$index], $unitTaxRates[$index]);
            }
        }
    }

    private function applyWithoutDistributionToUnits(OrderInterface $order, ZoneInterface $zone): void
    {
        foreach ($order->getItems() as $item) {
            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                continue;
            }

            /** @var OrderItemUnitInterface $unit */
            foreach ($item->getUnits() as $unit) {
                $taxAmount = $this->calculator->calculate($unit->getTotal(), $taxRate);
                if (0.00 === $taxAmount) {
                    continue;
                }

                $this->addAdjustment($unit, (int) $taxAmount, $taxRate);
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
