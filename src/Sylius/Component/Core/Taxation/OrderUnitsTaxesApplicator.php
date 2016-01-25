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

use Sylius\Bundle\CoreBundle\Distributor\IntegerDistributorInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
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
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var IntegerDistributorInterface
     */
    private $distributor;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param IntegerDistributorInterface $distributor
     */
    public function __construct(CalculatorInterface $calculator, AdjustmentFactoryInterface $adjustmentFactory, IntegerDistributorInterface $distributor)
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

        $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);
        $splitTaxes = $this->distributor->distribute($totalTaxAmount, $item->getUnits()->count());

        foreach ($splitTaxes as $key => $tax) {
            $this->addAdjustment($units->get($key), $tax, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
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
