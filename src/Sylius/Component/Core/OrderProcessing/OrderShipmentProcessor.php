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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Shipping\Exception\UnresolvedDefaultShippingMethodException;
use Sylius\Component\Shipping\Resolver\DefaultShippingMethodResolverInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

final class OrderShipmentProcessor implements OrderProcessorInterface
{
    private DefaultShippingMethodResolverInterface $defaultShippingMethodResolver;

    private FactoryInterface $shipmentFactory;

    private ?ShippingMethodsResolverInterface $shippingMethodsResolver;

    public function __construct(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        FactoryInterface $shipmentFactory,
        ?ShippingMethodsResolverInterface $shippingMethodsResolver = null
    ) {
        $this->defaultShippingMethodResolver = $defaultShippingMethodResolver;
        $this->shipmentFactory = $shipmentFactory;
        $this->shippingMethodsResolver = $shippingMethodsResolver;

        if (2 === func_num_args() || null === $shippingMethodsResolver) {
            @trigger_error(
                'Not passing ShippingMethodsResolverInterface explicitly is deprecated since 1.2 and will be prohibited in 2.0',
                \E_USER_DEPRECATED
            );
        }
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->isEmpty() || !$order->isShippingRequired()) {
            $order->removeShipments();

            return;
        }

        if ($order->hasShipments()) {
            $shipment = $this->getExistingShipmentWithProperMethod($order);

            if (null === $shipment) {
                return;
            }

            $this->processShipmentUnits($order, $shipment);

            return;
        }

        $this->createNewOrderShipment($order);
    }

    private function createNewOrderShipment(OrderInterface $order): void
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentFactory->createNew();
        $shipment->setOrder($order);

        try {
            $this->processShipmentUnits($order, $shipment);

            $shipment->setMethod($this->defaultShippingMethodResolver->getDefaultShippingMethod($shipment));

            $order->addShipment($shipment);
        } catch (UnresolvedDefaultShippingMethodException $exception) {
            foreach ($shipment->getUnits() as $unit) {
                $shipment->removeUnit($unit);
            }
        }
    }

    private function processShipmentUnits(BaseOrderInterface $order, ShipmentInterface $shipment): void
    {
        foreach ($shipment->getUnits() as $unit) {
            $shipment->removeUnit($unit);
        }

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        foreach ($order->getItemUnits() as $itemUnit) {
            if (null === $itemUnit->getShipment()) {
                $shipment->addUnit($itemUnit);
            }
        }
    }

    private function getExistingShipmentWithProperMethod(OrderInterface $order): ?ShipmentInterface
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();

        if (null === $this->shippingMethodsResolver) {
            return $shipment;
        }

        if (!in_array($shipment->getMethod(), $this->shippingMethodsResolver->getSupportedMethods($shipment), true)) {
            try {
                $shipment->setMethod($this->defaultShippingMethodResolver->getDefaultShippingMethod($shipment));
            } catch (UnresolvedDefaultShippingMethodException $exception) {
                return null;
            }
        }

        return $shipment;
    }
}
