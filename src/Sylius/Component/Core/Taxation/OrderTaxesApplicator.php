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

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * @var ZoneProviderInterface
     */
    protected $defaultTaxZoneProvider;

    /**
     * @var OrderTaxesByZoneApplicatorInterface
     */
    protected $orderShipmentTaxesApplicator;

    /**
     * @var OrderTaxesByZoneApplicatorInterface
     */
    protected $orderItemsTaxesApplicator;

    /**
     * @var ZoneMatcherInterface
     */
    protected $zoneMatcher;

    /**
     * @param ZoneProviderInterface $defaultTaxZoneProvider
     * @param OrderTaxesByZoneApplicatorInterface $orderShipmentTaxesApplicator
     * @param OrderTaxesByZoneApplicatorInterface $orderItemsTaxesApplicator
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(
        ZoneProviderInterface $defaultTaxZoneProvider,
        OrderTaxesByZoneApplicatorInterface $orderShipmentTaxesApplicator,
        OrderTaxesByZoneApplicatorInterface $orderItemsTaxesApplicator,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->defaultTaxZoneProvider = $defaultTaxZoneProvider;
        $this->orderShipmentTaxesApplicator = $orderShipmentTaxesApplicator;
        $this->orderItemsTaxesApplicator = $orderItemsTaxesApplicator;
        $this->zoneMatcher = $zoneMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order)
    {
        $this->clearTaxes($order);
        if ($order->isEmpty()) {
            return;
        }

        $zone = $this->getTaxZone($order->getShippingAddress());

        if (null === $zone) {
            return;
        }

        $this->orderItemsTaxesApplicator->apply($order, $zone);
        $this->orderShipmentTaxesApplicator->apply($order, $zone);
    }

    /**
     * @param AddressInterface|null $shippingAddress
     *
     * @return ZoneInterface|null
     */
    private function getTaxZone(AddressInterface $shippingAddress = null)
    {
        $zone = null;
        if (null !== $shippingAddress) {
            $zone = $this->zoneMatcher->match($shippingAddress);
        }

        return $zone ?: $this->defaultTaxZoneProvider->getZone();
    }

    /**
     * @param OrderInterface $order
     */
    private function clearTaxes(OrderInterface $order)
    {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        foreach ($order->getItems() as $item) {
            $item->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
        }
    }
}
