<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Taxation;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemsTaxesByZoneApplicator implements OrderItemsTaxesByZoneApplicatorInterface
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
     * @var IntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var TaxRateResolverInterface
     */
    private $taxRateResolver;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param IntegerDistributorInterface $distributor
     * @param TaxRateResolverInterface $taxRateResolver
     */
    public function __construct(CalculatorInterface $calculator, AdjustmentFactoryInterface $adjustmentFactory, IntegerDistributorInterface $distributor, TaxRateResolverInterface $taxRateResolver)
    {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->distributor = $distributor;
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

            $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);
            $splitTaxes = $this->distributor->distribute($totalTaxAmount, $quantity);

            $i = 0;
            foreach ($item->getUnits() as $unit) {
                if (0 === $splitTaxes[$i]) {
                    continue;
                }

                $this->addAdjustment($unit, $splitTaxes[$i], $taxRate->getLabel(), $taxRate->isIncludedInPrice());
                $i++;
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
