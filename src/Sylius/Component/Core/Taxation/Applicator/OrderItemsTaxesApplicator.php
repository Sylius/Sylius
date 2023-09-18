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
use Sylius\Component\Core\Model\OrderItemInterface;
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
        private ?ProportionalIntegerDistributorInterface $proportionalIntegerDistributor = null,
    ) {
        if ($this->proportionalIntegerDistributor === null) {
            trigger_deprecation(
                'sylius/core',
                '1.13',
                sprintf('Not passing an $proportionalIntegerDistributor to %s constructor is deprecated since Sylius 1.13 and will be prohibited in Sylius 2.0.', self::class),
            );
        }
    }

    /** @throws \InvalidArgumentException */
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $this->checkItemsQuantities($order);

        if ($this->proportionalIntegerDistributor === null) {
            $this->applyWithoutDistributionToItems($order, $zone);

            return;
        }

        $items = $order->getItems()->getValues();
        $itemTaxFloatAmounts = [];
        $itemTaxRates = [];

        foreach ($items as $index => $item) {
            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                $itemTaxFloatAmounts[$index] = 0;

                continue;
            }

            $itemTaxFloatAmounts[$index] = $this->calculator->calculate($item->getTotal(), $taxRate);
            $itemTaxRates[$index] = $taxRate;
        }

        $itemTaxWholeAmounts = array_map(fn (float $amount) => (int) round($amount), $itemTaxFloatAmounts);
        $itemTotalTaxWholeAmount = (int) round(array_sum($itemTaxFloatAmounts));
        $itemSplitTaxes = $this->proportionalIntegerDistributor->distribute($itemTaxWholeAmounts, $itemTotalTaxWholeAmount);

        foreach ($items as $index => $item) {
            if (0 === $itemSplitTaxes[$index] || !isset($itemTaxRates[$index])) {
                continue;
            }

            $this->distributeTaxesToUnits($itemSplitTaxes[$index], $item->getQuantity(), $item, $itemTaxRates[$index]);
        }
    }

    private function applyWithoutDistributionToItems(OrderInterface $order, ZoneInterface $zone): void
    {
        foreach ($order->getItems() as $item) {
            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                continue;
            }

            $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);

            $this->distributeTaxesToUnits($totalTaxAmount, $item->getQuantity(), $item, $taxRate);
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

    private function distributeTaxesToUnits(
        float $totalTaxAmount,
        int $quantity,
        OrderItemInterface $item,
        TaxRateInterface $taxRate,
    ): void {
        $unitSplitTaxes = $this->distributor->distribute($totalTaxAmount, $quantity);

        $units = $item->getUnits()->getValues();

        /** @var OrderItemUnitInterface $unit */
        foreach ($units as $index => $unit) {
            if (!array_key_exists($index, $unitSplitTaxes)) {
                $index = count($unitSplitTaxes) - 1;
            }

            if (0 === $unitSplitTaxes[$index]) {
                continue;
            }

            $this->addAdjustment($unit, $unitSplitTaxes[$index], $taxRate);
        }
    }

    private function checkItemsQuantities(OrderInterface $order): void
    {
        foreach ($order->getItems() as $item) {
            Assert::notSame($item->getQuantity(), 0, 'Cannot apply tax to order item with 0 quantity.');
        }
    }
}
