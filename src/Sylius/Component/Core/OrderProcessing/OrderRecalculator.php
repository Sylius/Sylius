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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\OrderTaxesApplicatorInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculator implements OrderRecalculatorInterface
{
    /**
     * @var DelegatingCalculatorInterface
     */
    private $priceCalculator;

    /**
     * @var OrderTaxesApplicatorInterface
     */
    private $taxesApplicator;

    /**
     * @var PromotionProcessorInterface
     */
    private $promotionProcessor;

    /**
     * @var ShippingChargesProcessorInterface
     */
    private $shippingChargesProcessor;

    /**
     * @param DelegatingCalculatorInterface $priceCalculator
     * @param OrderTaxesApplicatorInterface $taxesApplicator
     * @param PromotionProcessorInterface $promotionProcessor
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
     */
    public function __construct(
        DelegatingCalculatorInterface $priceCalculator,
        OrderTaxesApplicatorInterface $taxesApplicator,
        PromotionProcessorInterface $promotionProcessor,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->priceCalculator = $priceCalculator;
        $this->taxesApplicator = $taxesApplicator;
        $this->promotionProcessor = $promotionProcessor;
        $this->shippingChargesProcessor = $shippingChargesProcessor;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function recalculate(OrderInterface $order)
    {
        $this->recalculatePrices($order);
        $this->shippingChargesProcessor->applyShippingCharges($order);
        $this->promotionProcessor->process($order);
        $this->taxesApplicator->apply($order);
    }

    /**
     * @param OrderInterface $order
     */
    private function recalculatePrices(OrderInterface $order)
    {
        $context = [];
        if (null !== $customer = $order->getCustomer()) {
            $context['customer'] = $customer;
            $context['groups'] = $customer->getGroups()->toArray();
        }

        if (null !== $order->getChannel()) {
            $context['channel'] = [$order->getChannel()];
        }

        foreach ($order->getItems() as $item) {
            if ($item->isImmutable()) {
                continue;
            }

            $context['quantity'] = $item->getQuantity();
            $item->setUnitPrice($this->priceCalculator->calculate($item->getVariant(), $context));
        }
    }
}
