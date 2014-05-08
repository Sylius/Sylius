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

use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * Taxation processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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
        // Remove all tax adjustments, we recalculate everything from scratch.
        $order->removeTaxAdjustments();

        if ($order->getItems()->isEmpty()) {
            return;
        }

        $zone = null;

        if (null !== $order->getShippingAddress()) {
            // Match the tax zone.
            $zone = $this->zoneMatcher->match($order->getShippingAddress());
        }

        if ($this->settings->has('default_tax_zone')) {
            // If address does not match any zone, use the default one.
            $zone = $zone ?: $this->settings->get('default_tax_zone');
        }

        if (null === $zone) {
            return;
        }

        $taxes = $this->processTaxes($order, $zone);

        $this->addAdjustments($taxes, $order);

        $order->calculateTotal();
    }

    private function processTaxes(OrderInterface $order, $zone)
    {
        $taxes = array();
        foreach ($order->getItems() as $item) {
            $taxable = $item->getProduct();
            $rate = $this->taxRateResolver->resolve($taxable, array('zone' => $zone));

            if (null === $rate) {
                // Skip this item is there is not matching tax rate.
                continue;
            }

            $rateName = $rate->getName();

            $item->calculateTotal();

            $amount = $this->calculator->calculate($item->getTotal(), $rate);
            $taxAmount = $rate->getAmountAsPercentage();
            $description = sprintf('%s (%s%%)', $rateName, (float) $taxAmount);
            
            $taxes[$description] = array(
                'amount'   => (isset($taxes[$description]['amount']) ? $taxes[$description]['amount'] : 0) + $amount,
                'included' => $rate->isIncludedInPrice()
            );
        }

        return $taxes;
    }

    private function addAdjustments(array $taxes, OrderInterface $order)
    {
        foreach ($taxes as $description => $tax) {
            $adjustment = $this->adjustmentRepository->createNew();
            $adjustment->setLabel(OrderInterface::TAX_ADJUSTMENT);
            $adjustment->setAmount($tax['amount']);
            $adjustment->setDescription($description);
            $adjustment->setNeutral($tax['included']);

            $order->addAdjustment($adjustment);
        }
    }
}
