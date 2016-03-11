<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Taxation\Applicator;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class OrderItemUnitsTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var TaxRateResolverInterface
     */
    private $taxRateResolver;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param TaxRateResolverInterface $taxRateResolver
     */
    public function __construct(CalculatorInterface $calculator, AdjustmentFactoryInterface $adjustmentFactory, TaxRateResolverInterface $taxRateResolver)
    {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->taxRateResolver = $taxRateResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order, ZoneInterface $zone)
    {
        foreach ($order->getItems() as $item) {
            $quantity = $item->getQuantity();
            if (0 === $quantity) {
                continue;
            }

            $taxRate = $this->taxRateResolver->resolve($item->getVariant(), ['zone' => $zone]);

            if (null === $taxRate) {
                continue;
            }

            foreach ($item->getUnits() as $unit) {
                $taxAmount = $this->calculator->calculate($unit->getTotal(), $taxRate);
                if (0 === $taxAmount) {
                    continue;
                }

                $this->addAdjustment($unit, $taxAmount, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
            }
        }
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param int $taxAmount
     * @param string $label
     * @param bool $included
     */
    private function addAdjustment(OrderItemUnitInterface $unit, $taxAmount, $label, $included)
    {
        $unitTaxAdjustment = $this->adjustmentFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, $label, $taxAmount, $included);
        $unit->addAdjustment($unitTaxAdjustment);
    }
}
