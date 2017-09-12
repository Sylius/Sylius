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
    /**
     * @var DefaultShippingMethodResolverInterface
     */
    private $defaultShippingMethodResolver;

    /**
     * @var ShippingMethodsResolverInterface
     */
    private $shippingMethodsResolver;

    /**
     * @var FactoryInterface
     */
    private $shipmentFactory;

    /**
     * @param DefaultShippingMethodResolverInterface $defaultShippingMethodResolver
     * @param ShippingMethodsResolverInterface $shippingMethodsResolver
     * @param FactoryInterface $shipmentFactory
     */
    public function __construct(
        DefaultShippingMethodResolverInterface $defaultShippingMethodResolver,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        FactoryInterface $shipmentFactory
    ) {
        $this->defaultShippingMethodResolver = $defaultShippingMethodResolver;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if ($order->isEmpty() || !$order->isShippingRequired()) {
            $order->removeShipments();

            return;
        }

        $shipment = $this->getOrderShipment($order);

        if (null === $shipment) {
            return;
        }

        foreach ($shipment->getUnits() as $unit) {
            $shipment->removeUnit($unit);
        }

        foreach ($order->getItemUnits() as $itemUnit) {
            if (null === $itemUnit->getShipment()) {
                $shipment->addUnit($itemUnit);
            }
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return ShipmentInterface|null
     */
    private function getOrderShipment(OrderInterface $order): ?ShipmentInterface
    {
        if ($order->hasShipments()) {
            return $this->getExistingShipmentWithProperMethod($order);
        }

        try {
            /** @var ShipmentInterface $shipment */
            $shipment = $this->shipmentFactory->createNew();
            $shipment->setOrder($order);
            $shipment->setMethod($this->defaultShippingMethodResolver->getDefaultShippingMethod($shipment));

            $order->addShipment($shipment);

            return $shipment;
        } catch (UnresolvedDefaultShippingMethodException $exception) {
            return null;
        }
    }

    /**
     * @param OrderInterface $order
     *
     * @return ShipmentInterface|null
     */
    private function getExistingShipmentWithProperMethod(OrderInterface $order): ?ShipmentInterface
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $order->getShipments()->first();

        if (!in_array($shipment->getMethod(), $this->shippingMethodsResolver->getSupportedMethods($shipment))) {
            try {
                $shipment->setMethod($this->defaultShippingMethodResolver->getDefaultShippingMethod($shipment));
            } catch (UnresolvedDefaultShippingMethodException $exception) {
                return null;
            }
        }

        return $shipment;
    }
}
