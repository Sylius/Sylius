<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class ShipmentContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var FactoryInterface
     */
    private $shipmentFactory;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param RepositoryInterface $shipmentRepository
     * @param FactoryInterface $shipmentFactory
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        RepositoryInterface $shipmentRepository,
        FactoryInterface $shipmentFactory,
        SharedStorageInterface $sharedStorage
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentFactory = $shipmentFactory;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given there is a shipment using it
     * @Given /^there is a shipment using "([^"]*)" shipping method$/
     */
    public function thereIsShipmentUsingShippingMethod(ShippingMethodInterface $shippingMethod = null)
    {
        if (null === $shippingMethod) {
            $shippingMethod = $this->sharedStorage->getCurrentResource('shippingMethod');
        }

        $this->createShipment($shippingMethod);
    }

    /**
     * @param ShippingMethodInterface $shippingMethod
     * @param OrderInterface $order
     * @param string $state
     * @param array $units
     * @param string $tracking
     */
    private function createShipment(
        ShippingMethodInterface $shippingMethod = null,
        OrderInterface $order = null,
        $state = ShipmentInterface::STATE_SHIPPED,
        $units = [],
        $tracking = ''
    ) {
        if (null === $shippingMethod) {
            $shippingMethod = $this->sharedStorage->getCurrentResource('shippingMethod');
        }
        if (null === $order) {
            $order = $this->sharedStorage->getCurrentResource('order');
        }

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentFactory->createNew();
        $shipment->setMethod($shippingMethod);
        $shipment->setOrder($order);
        $shipment->setState($state);
        $shipment->setTracking($tracking);

        foreach ($units as $unit) {
            $shipment->addUnit($unit);
        }

        $this->shipmentRepository->add($shipment);
    }
}
