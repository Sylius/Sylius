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
use Sylius\Bundle\CoreBundle\Distributor\IntegerDistributorInterface;
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
            $quantity =  $item->getQuantity();
            if (0 === $quantity) {
                continue;
            }

            $taxRate = $this->taxRateResolver->resolve($item->getProduct(), array('zone' => $zone));

            if (null === $taxRate) {
                continue;
            }

            $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);
            $splitTaxes = $this->distributor->distribute($totalTaxAmount, $quantity);

            $units = $item->getUnits();
            foreach ($splitTaxes as $key => $tax) {
                if (0 === $tax) {
                    continue;
                }

                $unit = $this->getNextUnit($units);
                $this->addAdjustment($unit, $tax, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
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

    /**
     * @param Collection $units
     *
     * @return OrderItemUnitInterface
     */
    private function getNextUnit(Collection $units)
    {
        $unit = $units->current();
        if (null === $unit) {
            throw new \InvalidArgumentException('The number of tax items is greater than number of units.');
        }
        $units->next();

        return $unit;
    }
}
