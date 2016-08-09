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

use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Provider\ZoneProviderInterface;
use Sylius\Component\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Component\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class OrderTaxesProcessor implements OrderProcessorInterface
{
    /**
     * @var ZoneProviderInterface
     */
    private $defaultTaxZoneProvider;

    /**
     * @var ZoneMatcherInterface
     */
    private $zoneMatcher;

    /**
     * @var PrioritizedServiceRegistryInterface
     */
    private $strategyRegistry;

    /**
     * @param ZoneProviderInterface $defaultTaxZoneProvider
     * @param ZoneMatcherInterface $zoneMatcher
     * @param PrioritizedServiceRegistryInterface $strategyRegistry
     */
    public function __construct(
        ZoneProviderInterface $defaultTaxZoneProvider,
        ZoneMatcherInterface $zoneMatcher,
        PrioritizedServiceRegistryInterface $strategyRegistry
    ) {
        $this->defaultTaxZoneProvider = $defaultTaxZoneProvider;
        $this->zoneMatcher = $zoneMatcher;
        $this->strategyRegistry = $strategyRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
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

    /**
     * @param OrderInterface $order
     *
     * @return ZoneInterface|null
     */
    private function getTaxZone(OrderInterface $order)
    {
        $shippingAddress = $order->getShippingAddress();
        $zone = null;

        if (null !== $shippingAddress) {
            $zone = $this->zoneMatcher->match($shippingAddress);
        }

        return $zone ?: $this->defaultTaxZoneProvider->getZone($order);
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
