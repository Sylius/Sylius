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
use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException;
use Webmozart\Assert\Assert;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ShippingChargesProcessor implements OrderProcessorInterface
{
    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @var DelegatingCalculatorInterface
     */
    protected $shippingChargesCalculator;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param DelegatingCalculatorInterface $shippingChargesCalculator
     */
    public function __construct(FactoryInterface $adjustmentFactory, DelegatingCalculatorInterface $shippingChargesCalculator)
    {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->shippingChargesCalculator = $shippingChargesCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        /** @var CoreOrderInterface $order */
        Assert::isInstanceOf($order, CoreOrderInterface::class);

        // Remove all shipping adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        foreach ($order->getShipments() as $shipment) {
            try {
                $shippingCharge = $this->shippingChargesCalculator->calculate($shipment);

                $adjustment = $this->adjustmentFactory->createNew();
                $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT);
                $adjustment->setAmount($shippingCharge);
                $adjustment->setLabel($shipment->getMethod()->getName());

                $order->addAdjustment($adjustment);
            } catch (UndefinedShippingMethodException $exception) {}
        }
    }
}
