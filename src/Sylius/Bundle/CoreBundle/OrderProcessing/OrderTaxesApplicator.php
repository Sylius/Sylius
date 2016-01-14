<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\OrderProcessing;

use Sylius\Bundle\CoreBundle\Distributor\TaxesDistributorInterface;
use Sylius\Bundle\CoreBundle\Provider\DefaultTaxZoneProviderInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @var DefaultTaxZoneProviderInterface
     */
    protected $defaultTaxZoneProvider;

    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @var TaxesDistributorInterface
     */
    protected $orderUnitTaxesDistributor;

    /**
     * @var TaxRateResolverInterface
     */
    protected $taxRateResolver;

    /**
     * @var ZoneMatcherInterface
     */
    protected $zoneMatcher;

    /**
     * @param CalculatorInterface $calculator
     * @param DefaultTaxZoneProviderInterface $defaultTaxZoneProvider
     * @param FactoryInterface $adjustmentFactory
     * @param TaxesDistributorInterface $orderUnitTaxesDistributor
     * @param TaxRateResolverInterface $taxRateResolver
     * @param ZoneMatcherInterface $zoneMatcher
     */
    public function __construct(
        CalculatorInterface $calculator,
        DefaultTaxZoneProviderInterface $defaultTaxZoneProvider,
        FactoryInterface $adjustmentFactory,
        TaxesDistributorInterface $orderUnitTaxesDistributor,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher
    ) {
        $this->calculator = $calculator;
        $this->defaultTaxZoneProvider = $defaultTaxZoneProvider;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->orderUnitTaxesDistributor = $orderUnitTaxesDistributor;
        $this->taxRateResolver = $taxRateResolver;
        $this->zoneMatcher = $zoneMatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order)
    {
        if ($order->getItems()->isEmpty()) {
            return;
        }

        $this->clearAdjustments($order);
        $zone = $this->provideTaxZone($order->getShippingAddress());

        if (null === $zone) {
            return;
        }

        $this->processTaxes($order, $zone);
    }

    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     */
    protected function processTaxes(OrderInterface $order, ZoneInterface $zone)
    {
        foreach ($order->getItems() as $item) {
            $rate = $this->taxRateResolver->resolve($item->getProduct(), array('zone' => $zone));

            if (null === $rate) {
                continue;
            }

            $percentageAmount = $rate->getAmountAsPercentage();
            $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $rate);
            $label = sprintf('%s (%s%%)', $rate->getName(), (float) $percentageAmount);

            $splitTaxes = $this->orderUnitTaxesDistributor->distribute($item->getUnits()->count(), $totalTaxAmount);

            foreach ($splitTaxes as $key => $tax) {
                $this->addAdjustment($item->getUnits()->get($key), $tax, $label, $rate->isIncludedInPrice());
            }
        }
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param int $amount
     * @param string $label
     * @param bool $included
     */
    protected function addAdjustment(OrderItemUnitInterface $unit, $amount, $label, $included)
    {
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType(AdjustmentInterface::TAX_ADJUSTMENT);
        $adjustment->setAmount($amount);
        $adjustment->setDescription($label);
        $adjustment->setNeutral($included);

        $unit->addAdjustment($adjustment);
    }

    /**
     * @param AddressInterface|null $shippingAddress
     *
     * @return ZoneInterface|null
     */
    private function provideTaxZone(AddressInterface $shippingAddress = null)
    {
        $zone = null;
        if (null !== $shippingAddress) {
            $zone = $this->zoneMatcher->match($shippingAddress);
        }

        if (null === $zone) {
            $zone = $this->defaultTaxZoneProvider->provide();
        }

        return $zone;
    }

    /**
     * @param OrderInterface $order
     */
    private function clearAdjustments(OrderInterface $order)
    {
        foreach ($order->getItems() as $item) {
            $item->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
        }
    }
}
