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
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderShipmentTaxesApplicator implements OrderShipmentTaxesApplicatorInterface
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
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     */
    public function __construct(CalculatorInterface $calculator, AdjustmentFactoryInterface $adjustmentFactory)
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
        $taxAmount = $this->calculator->calculate($lastShipping->getAmount(), $taxRate);

        $this->addAdjustment($order, $taxAmount, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
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
        $shippingTaxAdjustment = $this->adjustmentFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, $label, $taxAmount, $included);
        $order->addAdjustment($shippingTaxAdjustment);
    }
}
