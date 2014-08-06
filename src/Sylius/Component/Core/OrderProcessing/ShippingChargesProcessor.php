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
use Sylius\Component\Resource\Manager\DomainManagerInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;

/**
 * Shipping charges processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingChargesProcessor implements ShippingChargesProcessorInterface
{
    /**
     * Adjustment manager.
     *
     * @var DomainManagerInterface
     */
    protected $manager;

    /**
     * Shipping charges calculator.
     *
     * @var DelegatingCalculatorInterface
     */
    protected $calculator;

    /**
     * Constructor.
     *
     * @param DomainManagerInterface        $manager
     * @param DelegatingCalculatorInterface $calculator
     */
    public function __construct(DomainManagerInterface $manager, DelegatingCalculatorInterface $calculator)
    {
        $this->manager = $manager;
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
            $adjustment = $this->manager->createNew();
            $adjustment->setLabel(AdjustmentInterface::SHIPPING_ADJUSTMENT);
            $adjustment->setAmount($this->calculator->calculate($shipment));
            $adjustment->setDescription($shipment->getMethod()->getName());

            $order->addAdjustment($adjustment);
        }

        $order->calculateTotal();
    }
}
