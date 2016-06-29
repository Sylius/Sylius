<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Taxation\Processor;

use Sylius\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Addressing\Model\AddressInterface;
use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Core\Model\AdjustmentInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Provider\ZoneProviderInterface;
use Sylius\Core\Taxation\Exception\UnsupportedTaxCalculationStrategyException;
use Sylius\Core\Taxation\Strategy\TaxCalculationStrategyInterface;
use Sylius\Registry\PrioritizedServiceRegistryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class OrderTaxesProcessor implements OrderTaxesProcessorInterface
{
    /**
     * @var ZoneProviderInterface
     */
    protected $defaultTaxZoneProvider;

    /**
     * @var ZoneMatcherInterface
     */
    protected $zoneMatcher;

    /**
     * @var PrioritizedServiceRegistryInterface
     */
    protected $strategyRegistry;

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

        $zone = $this->getTaxZone($order->getShippingAddress());

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
