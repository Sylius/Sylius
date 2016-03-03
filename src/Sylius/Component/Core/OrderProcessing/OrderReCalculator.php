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
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderRecalculator implements OrderRecalculatorInterface
{
    /**
     * @var PromotionProcessorInterface
     */
    private $promotionProcessor;

    /**
     * @var OrderTaxesApplicatorInterface
     */
    private $taxesApplicator;

    /**
     * @var ShippingChargesProcessorInterface
     */
    private $shippingChargesProcessor;

    /**
     * @param PromotionProcessorInterface $promotionProcessor
     * @param OrderTaxesApplicatorInterface $taxesApplicator
     * @param ShippingChargesProcessorInterface $shippingChargesProcessor
     */
    public function __construct(
        PromotionProcessorInterface $promotionProcessor,
        OrderTaxesApplicatorInterface $taxesApplicator,
        ShippingChargesProcessorInterface $shippingChargesProcessor
    ) {
        $this->promotionProcessor = $promotionProcessor;
        $this->taxesApplicator = $taxesApplicator;
        $this->shippingChargesProcessor = $shippingChargesProcessor;
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function recalculate(OrderInterface $order)
    {
        if (null === $order) {
            return;
        }
        $this->taxesApplicator->apply($order);
        $this->shippingChargesProcessor->applyShippingCharges($order);

        if (empty($promotions = $order->getPromotions())) {
            return;
        }

        $this->promotionProcessor->process($order);
    }
}
