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

use Sylius\Bundle\CoreBundle\OrderProcessing\OrderShipmentTaxesApplicatorInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\OrderUnitsTaxesApplicatorInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\DefaultTaxZoneProviderInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * @var DefaultTaxZoneProviderInterface
     */
    protected $defaultTaxZoneProvider;

    /**
     * @var OrderShipmentTaxesApplicatorInterface
     */
    protected $orderShipmentTaxesApplicator;

    /**
     * @var OrderUnitsTaxesApplicatorInterface
     */
    protected $orderUnitsTaxesApplicator;

    /**
     * @var TaxRateResolverInterface
     */
    protected $taxRateResolver;

    /**
     * @var ZoneMatcherInterface
     */
    protected $zoneMatcher;

    /**
     * @param DefaultTaxZoneProviderInterface $defaultTaxZoneProvider
     * @param OrderShipmentTaxesApplicatorInterface $orderShipmentTaxesApplicator
     * @param OrderUnitsTaxesApplicatorInterface $orderUnitsTaxesApplicator
     * @param TaxRateResolverInterface $taxRateResolver
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(
        DefaultTaxZoneProviderInterface $defaultTaxZoneProvider,
        OrderShipmentTaxesApplicatorInterface $orderShipmentTaxesApplicator,
        OrderUnitsTaxesApplicatorInterface $orderUnitsTaxesApplicator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->defaultTaxZoneProvider = $defaultTaxZoneProvider;
        $this->orderShipmentTaxesApplicator = $orderShipmentTaxesApplicator;
        $this->orderUnitsTaxesApplicator = $orderUnitsTaxesApplicator;
        $this->taxRateResolver = $taxRateResolver;
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

        $this->processUnitsTaxes($order, $zone);
        $this->processShipmentTaxes($order, $zone);
    }

    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     */
    protected function processUnitsTaxes(OrderInterface $order, ZoneInterface $zone)
    {
        foreach ($order->getItems() as $item) {
            $rate = $this->taxRateResolver->resolve($item->getProduct(), array('zone' => $zone));

            if (null === $rate) {
                continue;
            }

            $this->orderUnitsTaxesApplicator->apply($item, $rate);
        }
    }

    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     */
    protected function processShipmentTaxes(OrderInterface $order, ZoneInterface $zone)
    {
        $lastShipment = $order->getLastShipment();
        if (!$lastShipment) {
            return;
        }

        $rate = $this->taxRateResolver->resolve($lastShipment->getMethod(), array('zone' => $zone));

        if (null === $rate) {
            return;
        }

        $this->orderShipmentTaxesApplicator->apply($order, $rate);
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
