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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderProcessor implements OrderProcessorInterface
{
    /**
     * @var OrderShipmentProcessorInterface
     */
    private $orderShipmentProcessor;

    /**
     * @var PaymentProcessorInterface
     */
    private $paymentProcessor;

    /**
     * @param OrderShipmentProcessorInterface $orderShipmentProcessor
     * @param PaymentProcessorInterface $paymentProcessor
     */
    public function __construct(
        OrderShipmentProcessorInterface $orderShipmentProcessor, 
        PaymentProcessorInterface $paymentProcessor
    ) {
        $this->orderShipmentProcessor = $orderShipmentProcessor;
        $this->paymentProcessor = $paymentProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        $this->orderShipmentProcessor->processOrderShipment($order);
        $this->paymentProcessor->processOrderPayments($order);
    }
}
