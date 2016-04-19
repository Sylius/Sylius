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

use Sylius\Component\Core\Distributor\IntegerDistributorInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRatesResolverInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
class OrderItemsTaxesApplicator implements OrderTaxesApplicatorInterface
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
     * @var IntegerDistributorInterface
     */
    private $distributor;

    /**
     * @var TaxRatesResolverInterface
     */
    private $taxRatesResolver;

    /**
     * @param CalculatorInterface $calculator
     * @param AdjustmentFactoryInterface $adjustmentFactory
     * @param IntegerDistributorInterface $distributor
     * @param TaxRatesResolverInterface $taxRatesResolver
     */
    public function __construct(
        CalculatorInterface $calculator,
        AdjustmentFactoryInterface $adjustmentFactory,
        IntegerDistributorInterface $distributor,
        TaxRatesResolverInterface $taxRatesResolver
    ) {
        $this->calculator = $calculator;
        $this->adjustmentFactory = $adjustmentFactory;
        $this->distributor = $distributor;
        $this->taxRatesResolver = $taxRatesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(OrderInterface $order, ZoneInterface $zone)
    {
        foreach ($order->getItems() as $item) {
            $quantity = $item->getQuantity();
            if (0 === $quantity) {
                throw new \InvalidArgumentException('Cannot apply tax to order item with 0 quantity.');
            }

            $taxRates = $this->taxRatesResolver->resolve($item->getVariant(), ['zone' => $zone]);

            foreach ($taxRates as $taxRate) {
                if ($this->removeUnnecessaryIncludedTax($taxRate, $item, $order->getCustomer())) {
                    continue;
                }

                $totalTaxAmount = $this->calculator->calculate($item->getTotal(), $taxRate);

                $splitTaxes = $this->distributor->distribute($totalTaxAmount, $quantity);

                $i = 0;
                foreach ($item->getUnits() as $unit) {
                    if (0 === $splitTaxes[$i] && $taxRate->getAmount() > 0) {
                        continue;
                    }

                    $this->addAdjustment($unit, $splitTaxes[$i], $taxRate->getLabel(), $taxRate->isIncludedInPrice());
                    $i++;
                }
            }
        }
    }

    /**
     * @param TaxRateInterface       $taxRate
     * @param OrderItemInterface     $item
     * @param CustomerInterface|null $customer
     *
     * @return bool
     */
    private function removeUnnecessaryIncludedTax(
        TaxRateInterface $taxRate,
        OrderItemInterface $item,
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

        $includedTaxAmount = $this->calculator->calculate($item->getUnitPrice(), $taxRate);
        $item->setUnitPrice($item->getUnitPrice() - $includedTaxAmount);

        return true;
    }

    /**
     * @param OrderItemUnitInterface $unit
     * @param int $taxAmount
     * @param string $label
     * @param bool $included
     */
    private function addAdjustment(OrderItemUnitInterface $unit, $taxAmount, $label, $included)
    {
        $unitTaxAdjustment = $this->adjustmentFactory->createWithData(AdjustmentInterface::TAX_ADJUSTMENT, $label, $taxAmount, $included);
        $unit->addAdjustment($unitTaxAdjustment);
    }
}
