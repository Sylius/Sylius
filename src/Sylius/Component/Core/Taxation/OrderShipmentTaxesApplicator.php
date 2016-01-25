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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderShipmentTaxesApplicator implements OrderShipmentTaxesApplicatorInterface
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
     * @param CalculatorInterface $calculator
     * @param FactoryInterface $adjustmentFactory
     */
    public function __construct(CalculatorInterface $calculator, FactoryInterface $adjustmentFactory)
    {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order, TaxRateInterface $taxRate)
    {
        $shippingAdjustments = $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        if ($shippingAdjustments->isEmpty()) {
            return;
        }

        $lastShipping = $shippingAdjustments->last();

        $percentageAmount = $taxRate->getAmountAsPercentage();
        $taxAmount = $this->calculator->calculate($lastShipping->getAmount(), $taxRate);
        $label = sprintf('%s (%s%%)', $taxRate->getName(), (float) $percentageAmount);

        $this->addAdjustment($order, $taxAmount, $label, $taxRate->isIncludedInPrice());
    }

    /**
     * @param OrderInterface $order
     * @param int $taxAmount
     * @param string $label
     * @param bool $included
     */
    private function addAdjustment($order, $taxAmount, $label, $included)
    {
        /** @var AdjustmentInterface $shippingTaxAdjustment */
        $shippingTaxAdjustment = $this->adjustmentFactory->createNew();
        $shippingTaxAdjustment->setType(AdjustmentInterface::TAX_ADJUSTMENT);
        $shippingTaxAdjustment->setDescription($label);
        $shippingTaxAdjustment->setAmount($taxAmount);
        $shippingTaxAdjustment->setNeutral($included);

        $order->addAdjustment($shippingTaxAdjustment);
    }
}
