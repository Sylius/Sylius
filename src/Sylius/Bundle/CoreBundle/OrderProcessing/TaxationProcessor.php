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

use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Order\Model\AdjustmentDTO;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxationProcessor implements TaxationProcessorInterface
{
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
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param CalculatorInterface      $calculator
     * @param TaxRateResolverInterface $taxRateResolver
     * @param ZoneMatcherInterface     $zoneMatcher
     * @param Settings                 $taxationSettings
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        CalculatorInterface $calculator,
        TaxRateResolverInterface $taxRateResolver,
        ZoneMatcherInterface $zoneMatcher,
        Settings $taxationSettings,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->calculator = $calculator;
        $this->taxRateResolver = $taxRateResolver;
        $this->zoneMatcher = $zoneMatcher;
        $this->settings = $taxationSettings;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function removeTaxes(OrderInterface $order)
    {
        foreach ($order->getInventoryUnits() as $inventoryUnit) {
            $inventoryUnit->removeAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        }

        $order->calculateTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function applyTaxes(OrderInterface $order)
    {
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

    /**
     * @param OrderInterface $order
     * @param ZoneInterface|string $zone
     *
     * @return array
     */
    protected function processTaxes(OrderInterface $order, $zone)
    {
        $taxes = array();

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $rate = $this->taxRateResolver->resolve($item->getProduct(), array('zone' => $zone));

            // Skip this item is there is not matching tax rate.
            if (null === $rate) {
                continue;
            }

            $amount = $this->calculator->calculate($item->getUnitPrice(), $rate);
            $taxAmount = $rate->getAmountAsPercentage();
            $description = sprintf('%s (%s%%)', $rate->getName(), (float) $taxAmount);

            /** @var InventoryUnitInterface $inventoryUnit */
            foreach ($item->getInventoryUnits() as $inventoryUnit) {
                $taxes[] = array(
                    'originId' => $rate->getId(),
                    'originType' => get_class($rate),
                    'amount' => $amount,
                    'inventoryUnit' => $inventoryUnit,
                    'description' => $description,
                    'neutrality' => $rate->isIncludedInPrice(),
                );
            }
        }

        return $taxes;
    }

    /**
     * @param array $taxes
     */
    protected function addAdjustments(array $taxes)
    {
        foreach ($taxes as $tax) {
            $adjustmentDTO = new AdjustmentDTO();
            $adjustmentDTO->type = AdjustmentInterface::TAX_ADJUSTMENT;
            $adjustmentDTO->amount = $tax['amount'];
            $adjustmentDTO->description = $tax['description'];
            $adjustmentDTO->neutrality = $tax['neutrality'];
            $adjustmentDTO->originId = $tax['originId'];
            $adjustmentDTO->originType = $tax['originType'];

            $this->eventDispatcher->dispatch(
                AdjustmentEvent::ADJUSTMENT_ADDING_INVENTORY_UNIT,
                new AdjustmentEvent(
                    $tax['inventoryUnit'],
                    [
                        'adjustment-data' => $adjustmentDTO,
                    ]
                )
            );
        }
    }
}
