<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\ShippingBundle\Calculator\DelegatingCalculatorInterface;

/**
 * Shipping charges processor.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingChargesProcessor implements ShippingChargesProcessorInterface
{
    /**
     * Adjustment repository.
     *
     * @var RepositoryInterface
     */
    protected $adjustmentRepository;

    /**
     * Shipping charges calculator.
     *
     * @var DelegatingCalculatorInterface
     */
    protected $calculator;

    /**
     * Constructor.
     *
     * @param RepositoryInterface           $adjustmentRepository
     * @param DelegatingCalculatorInterface $calculator
     */
    public function __construct(RepositoryInterface $adjustmentRepository, DelegatingCalculatorInterface $calculator)
    {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function applyShippingCharges(OrderInterface $order)
    {
        $order->removeShippingAdjustments(); // Remove all shipping adjustments, we recalculate everything from scratch.

        foreach ($order->getShipments() as $shipment) {
            $shippingCharge = $this->calculator->calculate($shipment);

            $adjustment = $this->adjustmentRepository->createNew();
            $adjustment->setLabel(OrderInterface::SHIPPING_ADJUSTMENT);
            $adjustment->setAmount($shippingCharge);
            $adjustment->setDescription($shipment->getMethod()->getName());

            $order->addAdjustment($adjustment);
        }

        $order->calculateTotal();
    }
}
