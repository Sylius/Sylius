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

use Sylius\Bundle\CoreBundle\Distributor\TaxesDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderUnitsTaxesApplicator implements OrderUnitsTaxesApplicatorInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var TaxesDistributorInterface
     */
    private $distributor;

    /**
     * @param CalculatorInterface $calculator
     * @param FactoryInterface $adjustmentFactory
     * @param TaxesDistributorInterface $distributor
     */
    public function __construct(CalculatorInterface $calculator, FactoryInterface $adjustmentFactory, TaxesDistributorInterface $distributor)
    {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->distributor = $distributor;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderItemInterface $item, TaxRateInterface $taxRate)
    {
        $units = $item->getUnits();
        if ($units->isEmpty()) {
            return;
        }

        $percentageAmount = $taxRate->getAmountAsPercentage();
        $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);
        $label = sprintf('%s (%s%%)', $taxRate->getName(), (float) $percentageAmount);

        $splitTaxes = $this->distributor->distribute($totalTaxAmount, $item->getUnits()->count());

        foreach ($splitTaxes as $key => $tax) {
            $this->addAdjustment($units->get($key), $tax, $label, $taxRate->isIncludedInPrice());
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
        /** @var AdjustmentInterface $unitTaxAdjustment */
        $unitTaxAdjustment = $this->adjustmentFactory->createNew();
        $unitTaxAdjustment->setType(AdjustmentInterface::TAX_ADJUSTMENT);
        $unitTaxAdjustment->setDescription($label);
        $unitTaxAdjustment->setAmount($taxAmount);
        $unitTaxAdjustment->setNeutral($included);

        $unit->addAdjustment($unitTaxAdjustment);
    }
}
