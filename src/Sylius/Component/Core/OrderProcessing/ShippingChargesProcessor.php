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
    public function __construct(private FactoryInterface $adjustmentFactory, private DelegatingCalculatorInterface $shippingChargesCalculator)
    {
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (OrderInterface::STATE_CART !== $order->getState()) {
            return;
        }

        // Remove all shipping adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        foreach ($order->getShipments() as $shipment) {
            $shipment->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

            try {
                $shippingCharge = $this->shippingChargesCalculator->calculate($shipment);
                $shippingMethod = $shipment->getMethod();

                /** @var AdjustmentInterface $adjustment */
                $adjustment = $this->adjustmentFactory->createNew();
                $adjustment->setType(AdjustmentInterface::SHIPPING_ADJUSTMENT);
                $adjustment->setAmount($shippingCharge);
                $adjustment->setLabel($shippingMethod->getName());
                $adjustment->setDetails([
                    'shippingMethodCode' => $shippingMethod->getCode(),
                    'shippingMethodName' => $shippingMethod->getName(),
                ]);

                $shipment->addAdjustment($adjustment);
            } catch (UndefinedShippingMethodException) {
            }
        }
    }
}
