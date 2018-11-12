<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException;
use Webmozart\Assert\Assert;

final class ShippingChargesProcessor implements OrderProcessorInterface
{
    /**
     * @var FactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var DelegatingCalculatorInterface
     */
    private $shippingChargesCalculator;

    public function __construct(
        FactoryInterface $adjustmentFactory,
        DelegatingCalculatorInterface $shippingChargesCalculator
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
        $this->shippingChargesCalculator = $shippingChargesCalculator;
    }

    /**
     * {@inheritdoc}
     */
    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        // Remove all shipping adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        foreach ($order->getShipments() as $shipment) {
            try {
                $shippingCharge = $this->shippingChargesCalculator->calculate($shipment);

                /** @var AdjustmentInterface $adjustment */
                $adjustment = $this->adjustmentFactory->createNew();
                $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT);
                $adjustment->setAmount($shippingCharge);
                $adjustment->setLabel($shipment->getMethod()->getName());

                $order->addAdjustment($adjustment);
            } catch (UndefinedShippingMethodException $exception) {
            }
        }
    }
}
