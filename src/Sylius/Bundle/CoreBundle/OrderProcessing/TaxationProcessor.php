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
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Resource\Manager\DomainManagerInterface;
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
     * Adjustment manager.
     *
     * @var DomainManagerInterface
     */
    protected $adjustmentManager;

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
     * @param DomainManagerInterface   $adjustmentManager
     * @param CalculatorInterface      $calculator
     * @param TaxRateResolverInterface $taxRateResolver
     * @param ZoneMatcherInterface     $zoneMatcher
     * @param Settings                 $taxationSettings
     */
    public function __construct(
        DomainManagerInterface $adjustmentManager,
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings
    )
    {
        $this->adjustmentManager = $adjustmentManager;
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
        $order->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);

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
            $rate = $this->taxRateResolver->resolve($item->getProduct(), array('zone' => $zone));

            // Skip this item is there is not matching tax rate.
            if (null === $rate) {
                continue;
            }

            $item->calculateTotal();

            $amount = $this->calculator->calculate($item->getTotal(), $rate);
            $description = sprintf('%s (%s%%)', $rate->getName(), (float) $rate->getAmountAsPercentage());

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
            /** @var $adjustment AdjustmentInterface */
            $adjustment = $this->adjustmentManager->createNew();
            $adjustment->setLabel(AdjustmentInterface::TAX_ADJUSTMENT);
            $adjustment->setAmount($tax['amount']);
            $adjustment->setDescription($description);
            $adjustment->setNeutral($tax['included']);

            $order->addAdjustment($adjustment);
        }
    }
}
