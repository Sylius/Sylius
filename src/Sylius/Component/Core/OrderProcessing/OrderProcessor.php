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
     * @param OrderShipmentProcessorInterface $orderShipmentProcessor
     */
    public function __construct(OrderShipmentProcessorInterface $orderShipmentProcessor)
    {
        $this->orderShipmentProcessor = $orderShipmentProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(OrderInterface $order)
    {
        $this->orderShipmentProcessor->processOrderShipment($order);
    }
}
