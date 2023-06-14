<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Scope;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;
use Webmozart\Assert\Assert;

final class OrderTaxesProcessor implements OrderProcessorInterface
{
    public function __construct(
        private ZoneProviderInterface $defaultTaxZoneProvider,
        private ZoneMatcherInterface $zoneMatcher,
        private PrioritizedServiceRegistryInterface $strategyRegistry,
        private ?TaxationAddressResolverInterface $taxationAddressResolver = null,
    ) {
        if ($this->taxationAddressResolver === null) {
            @trigger_error(sprintf('Not passing a $taxationAddressResolver to %s constructor is deprecated since Sylius 1.11 and will be removed in Sylius 2.0.', self::class), \E_USER_DEPRECATED);
        }
    }

    public function process(BaseOrderInterface $order): void
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        if (!$order->canBeProcessed()) {
            return;
        }

        $this->clearTaxes($order);
        if ($order->isEmpty()) {
            return;
        }

        $zone = $this->getTaxZone($order);

        if (null === $zone) {
            return;
        }

        /** @var TaxCalculationStrategyInterface $strategy */
        foreach ($this->strategyRegistry->all() as $strategy) {
            if ($strategy->supports($order, $zone)) {
                $strategy->applyTaxes($order, $zone);

                return;
            }
        }

        throw new UnsupportedTaxCalculationStrategyException();
    }

    private function getTaxZone(OrderInterface $order): ?ZoneInterface
    {
        $taxationAddress = $order->getBillingAddress();

        if ($this->taxationAddressResolver) {
            $taxationAddress = $this->taxationAddressResolver->getTaxationAddressFromOrder($order);
        }

        $zone = null;

        if (null !== $taxationAddress) {
            $zone = $this->zoneMatcher->match($taxationAddress, Scope::TAX);
        }

        return $zone ?: $this->defaultTaxZoneProvider->getZone($order);
    }

    private function clearTaxes(OrderInterface $order): void
    {
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

        foreach ($order->getItems() as $item) {
            $item->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
        }

        /** @var ShipmentInterface $shipment */
        foreach ($order->getShipments() as $shipment) {
            $shipment->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        }
    }
}
