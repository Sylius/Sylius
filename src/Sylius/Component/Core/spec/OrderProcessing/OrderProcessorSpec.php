<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderProcessorInterface;
use Sylius\Component\Core\OrderProcessing\OrderShipmentProcessorInterface;
use Sylius\Component\Core\OrderProcessing\PaymentProcessorInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderProcessorSpec extends ObjectBehavior
{
    function let(
        OrderShipmentProcessorInterface $orderShipmentProcessor,
        PaymentProcessorInterface $paymentProcessor
    ) {
        $this->beConstructedWith($orderShipmentProcessor, $paymentProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\OrderProcessor');
    }

    function it_implements_order_processor_interface()
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_runs_order_shipment_processor_and_payment_processor_to_control_order_shipments_and_payments(
        OrderInterface $order,
        OrderShipmentProcessorInterface $orderShipmentProcessor,
        PaymentProcessorInterface $paymentProcessor
    ) {
        $orderShipmentProcessor->processOrderShipment($order)->shouldBeCalled();
        $paymentProcessor->processOrderPayments($order)->shouldBeCalled();

        $this->process($order);
    }
}
