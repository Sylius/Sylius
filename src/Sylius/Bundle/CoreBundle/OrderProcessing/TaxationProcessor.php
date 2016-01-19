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
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationProcessor implements TaxationProcessorInterface
{
    /**
     * @var FactoryInterface
     */
    protected $adjustmentFactory;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @var TaxRateResolverInterface
     */
    protected $taxRateResolver;

    /**
     * @var ZoneMatcherInterface
     */
    protected $zoneMatcher;

    /**
     * @var Settings
     */
    protected $settings;

    /**
     * @param FactoryInterface $adjustmentFactory
     * @param CalculatorInterface $calculator
     * @param TaxRateResolverInterface $taxRateResolver
     * @param ZoneMatcherInterface $zoneMatcher
     * @param Settings $taxationSettings
     */
    public function __construct(
        FactoryInterface $adjustmentFactory,
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings
    ) {
        $this->adjustmentFactory = $adjustmentFactory;
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
    }

    /**
     * @param OrderInterface $order
     * @param ZoneInterface $zone
     *
     * @return array
     */
    protected function processTaxes(OrderInterface $order, $zone)
    {
        $taxes = array();
        foreach ($order->getItems() as $item) {
            $rate = $this->taxRateResolver->resolve($item->getProduct(), array('zone' => $zone));

            // Skip this item is there is not matching tax rate.
            if (null === $rate) {
                continue;
            }

            $amount = $this->calculator->calculate($item->getTotal(), $rate);
            $taxAmount = $rate->getAmountAsPercentage();
            $description = sprintf('%s (%s%%)', $rate->getName(), (float) $taxAmount);

            $taxes[$description] = array(
                'amount'   => (isset($taxes[$description]['amount']) ? $taxes[$description]['amount'] : 0) + $amount,
                'included' => $rate->isIncludedInPrice()
            );
        }

        return $taxes;
    }

    /**
     * @param array $taxes
     * @param OrderInterface $order
     */
    protected function addAdjustments(array $taxes, OrderInterface $order)
    {
        foreach ($taxes as $description => $tax) {
            $adjustment = $this->adjustmentFactory->createNew();
            $adjustment->setType(AdjustmentInterface::TAX_ADJUSTMENT);
            $adjustment->setAmount($tax['amount']);
            $adjustment->setDescription($description);
            $adjustment->setNeutral($tax['included']);

            $order->addAdjustment($adjustment);
        }
    }
}
