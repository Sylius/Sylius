<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Taxation\Applicator;

use Sylius\Addressing\Model\ZoneInterface;
use Sylius\Core\Model\AdjustmentInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Taxation\Calculator\CalculatorInterface;
use Sylius\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class OrderShipmentTaxesApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * @var AdjustmentFactoryInterface
     */
    private $adjustmentFactory;

    /**
     * @var TaxRateResolverInterface
     */
    private $taxRateResolver;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param TaxRateResolverInterface $taxRateResolver
     */
    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        TaxRateResolverInterface $taxRateResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->taxRateResolver = $taxRateResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order, ZoneInterface $zone)
    {
        $lastShipment = $order->getLastShipment();
        if (!$lastShipment) {
            return;
        }

        $shippingAdjustments = $order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);
        if ($shippingAdjustments->isEmpty()) {
            return;
        }

        $taxRate = $this->taxRateResolver->resolve($lastShipment->getMethod(), ['zone' => $zone]);
        if (null === $taxRate) {
            return;
        }

        $shippingPrice = $order->getShippingTotal();

        $taxAmount = $this->calculator->calculate($shippingPrice, $taxRate);
        if (0 === $taxAmount) {
            return;
        }

        $this->addAdjustment($order, $taxAmount, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
    }

    /**
     * @param OrderInterface $order
     * @param int $taxAmount
     * @param string $label
     * @param bool $included
     */
    private function addAdjustment($order, $taxAmount, $label, $included)
    {
        /** @var AdjustmentInterface $shippingTaxAdjustment */
        $shippingTaxAdjustment = $this->adjustmentFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, $label, $taxAmount, $included);
        $order->addAdjustment($shippingTaxAdjustment);
    }
}
