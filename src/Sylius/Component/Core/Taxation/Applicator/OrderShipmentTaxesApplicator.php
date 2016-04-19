<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Taxation\Applicator;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\TaxRatesInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRatesResolverInterface;

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
     * @var TaxRatesResolverInterface
     */
    private $taxRatesResolver;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param TaxRatesResolverInterface $taxRatesResolver
     */
    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        TaxRatesResolverInterface $taxRatesResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->taxRatesResolver = $taxRatesResolver;
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

        $taxRates = $this->taxRatesResolver->resolve($lastShipment->getMethod(), ['zone' => $zone]);

        foreach($taxRates as $taxRate) {
            $lastShippingAdjustment = $shippingAdjustments->last();

//            if ($this->removeUnnecessaryIncludedTax($taxRate, $lastShippingAdjustment, $order->getCustomer())) {
//                continue;
//            }

            $taxAmount = $this->calculator->calculate($lastShippingAdjustment->getAmount(), $taxRate);
            if (0 === $taxAmount && $taxRate->getAmount() > 0) {
                return;
            }

            $this->addAdjustment($order, $taxAmount, $taxRate->getLabel(), $taxRate->isIncludedInPrice());
        }
    }

    /**
     * @param TaxRateInterface       $taxRate
     * @param AdjustmentInterface    $adjustment
     * @param CustomerInterface|null $customer
     *
     * @return bool
     */
    private function removeUnnecessaryIncludedTax(
        TaxRateInterface $taxRate,
        AdjustmentInterface $adjustment,
        CustomerInterface $customer = null
    ) {
        if (!$taxRate->isIncludedInPrice() || !$customer) {
            return false;
        }

        if ($customer->isEntrepreneurOrReseller() && $taxRate->isAppliedToEntrepreneursAndResellers()) {
            return false;
        }

        if ($customer->isIndividual() && $taxRate->isAppliedToIndividuals()) {
            return false;
        }

        $includedTaxAmount = $this->calculator->calculate($adjustment->getAmount(), $taxRate);
        $adjustment->setAmount($adjustment->getAmount() - $includedTaxAmount);

        return true;
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
