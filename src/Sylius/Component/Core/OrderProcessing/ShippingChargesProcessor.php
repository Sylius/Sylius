<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;

/**
 * Shipping charges processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingChargesProcessor implements ShippingChargesProcessorInterface
{
    /**
     * Adjustment repository.
     *
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * Shipping charges calculator.
     *
     * @var DelegatingCalculatorInterface
     */
    protected $calculator;

    /**
     * Constructor.
     *
     * @param FactoryInterface $adjustmentFactory
     * @param DelegatingCalculatorInterface $calculator
     */
    public function __construct(FactoryInterface $adjustmentFactory, DelegatingCalculatorInterface $calculator)
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function applyShippingCharges(OrderInterface $order)
    {
        // Remove all shipping adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        foreach ($order->getShipments() as $shipment) {
            $shippingCharge = $this->calculator->calculate($shipment);

            $adjustment = $this->adjustmentFactory->createNew();
            $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT);
            $adjustment->setAmount($shippingCharge);
            $adjustment->setLabel($shipment->getMethod()->getName());

            $order->addAdjustment($adjustment);
        }
    }
}
