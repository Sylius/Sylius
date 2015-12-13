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

use Sylius\Bundle\CoreBundle\Event\AdjustmentEvent;
use Sylius\Bundle\CoreBundle\EventListener\AdjustmentSubscriber;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\AdjustmentDTO;
use Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Shipping charges processor.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingChargesProcessor implements ShippingChargesProcessorInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * Shipping charges calculator.
     *
     * @var DelegatingCalculatorInterface
     */
    protected $calculator;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param DelegatingCalculatorInterface $calculator
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, DelegatingCalculatorInterface $calculator)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->calculator = $calculator;
    }

    /**
     * {@inheritdoc}
     */
    public function applyShippingCharges(OrderInterface $order)
    {
        // Remove all shipping adjustments, we recalculate everything from scratch.
        $order->removeAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT);

        foreach ($order->getShipments() as $shipment) {
            $shippingCharge = $this->calculator->calculate($shipment);

            $adjustmentDTO = new AdjustmentDTO();
            $adjustmentDTO->type = AdjustmentInterface::SHIPPING_ADJUSTMENT;
            $adjustmentDTO->amount = $shippingCharge;
            $adjustmentDTO->description = $shipment->getMethod()->getName();

            $this->eventDispatcher->dispatch(
                AdjustmentEvent::ADJUSTMENT_ADDING_ORDER,
                new AdjustmentEvent(
                    $order,
                    [AdjustmentSubscriber::EVENT_ARGUMENT_DATA_KEY => $adjustmentDTO]
                )
            );
        }
    }
}
