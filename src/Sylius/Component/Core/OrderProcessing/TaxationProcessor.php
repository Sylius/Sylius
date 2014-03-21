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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * Taxation processor.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxationProcessor implements TaxationProcessorInterface
{
    /**
     * Adjustment repository.
     *
     * @var RepositoryInterface
     */
    protected $adjustmentRepository;

    /**
     * Tax calculator.
     *
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * Tax rate resolver.
     *
     * @var TaxRateResolverInterface
     */
    protected $taxRateResolver;

    /**
     * Zone matcher.
     *
     * @var ZoneMatcherInterface
     */
    protected $zoneMatcher;

    /**
     * Taxation settings.
     *
     * @var Settings
     */
    protected $settings;

    /**
     * Constructor.
     *
     * @param RepositoryInterface      $adjustmentRepository
     * @param CalculatorInterface      $calculator
     * @param TaxRateResolverInterface $taxRateResolver
     * @param ZoneMatcherInterface     $zoneMatcher
     * @param Settings                 $taxationSettings
     */
    public function __construct(
        RepositoryInterface $adjustmentRepository,
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings
    )
    {
        $this->adjustmentRepository = $adjustmentRepository;
        $this->calculator = $calculator;
        $this->taxRateResolver = $taxRateResolver;
        $this->zoneMatcher = $zoneMatcher;
        $this->settings = $taxationSettings;

    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxes(OrderInterface $order)
    {
        $order->removeTaxAdjustments(); // Remove all tax adjustments, we recalculate everything from scratch.

        if (0 === count($order->getItems())) {
            return;
        }

        $zone = null;

        if (null !== $order->getShippingAddress()) {
            $zone = $this->zoneMatcher->match($order->getShippingAddress()); // Match the tax zone.
        }

        if ($this->settings->has('default_tax_zone')) {
            $zone = $zone ?: $this->settings->get('default_tax_zone'); // If address does not match any zone, use the default one.
        }

        if (null === $zone) {
            return;
        }

        $taxes = array();

        foreach ($order->getItems() as $item) {
            $taxable = $item->getProduct();
            $rate = $this->taxRateResolver->resolve($taxable, array('zone' => $zone));

            if (null === $rate) {
                continue; // Skip this item is there is not matching tax rate.
            }

            $rateName = $rate->getName();

            $item->calculateTotal();

            $amount = $this->calculator->calculate($item->getTotal(), $rate);
            $taxAmount = $rate->getAmountAsPercentage();
            $description = sprintf('%s (%d%%)', $rateName, $taxAmount);

            $taxes[$description] = array(
                'amount'   => (isset($taxes[$description]['amount']) ? $taxes[$description]['amount'] : 0) + $amount,
                'included' => $rate->isIncludedInPrice()
            );
        }

        foreach ($taxes as $description => $tax) {
            $adjustment = $this->adjustmentRepository->createNew();

            $adjustment->setLabel(OrderInterface::TAX_ADJUSTMENT);
            $adjustment->setAmount($tax['amount']);
            $adjustment->setDescription($description);
            $adjustment->setNeutral($tax['included']);

            $order->addAdjustment($adjustment);
        }

        $order->calculateTotal();
    }
}
