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

namespace Sylius\Component\Core\Taxation\Applicator;

use Sylius\Component\Addressing\Model\ZoneInterface;
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
        private TaxRateResolverInterface $taxRateResolver
    ) {
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        foreach ($order->getItems() as $item) {
            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);
            if (null === $taxRate) {
                continue;
            }

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
            ]
        );
        $unit->addAdjustment($unitTaxAdjustment);
    }
}
