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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\OrderExchangeRateProcessor;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\OrderProcessing\OrderProcessorInterface;
use Sylius\Component\Core\Updater\OrderUpdaterInterface;

/**
 * @mixin OrderExchangeRateProcessor
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class OrderExchangeRateProcessorSpec extends ObjectBehavior
{
    function let(OrderUpdaterInterface $orderUpdater)
    {
        $this->beConstructedWith($orderUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderExchangeRateProcessor::class);
    }

    function it_implements_order_processor_interface()
    {
        $this->shouldImplement(OrderProcessorInterface::class);
    }

    function it_does_nothing_when_the_order_is_cancelled(OrderUpdaterInterface $orderUpdater, OrderInterface $order)
    {
        $order->getState()->willReturn(OrderInterface::STATE_CANCELLED);

        $orderUpdater->update(Argument::any())->shouldNotBeCalled();

        $this->process($order);
    }

    function it_processes_the_order(OrderUpdaterInterface $orderUpdater, OrderInterface $order)
    {
        $order->getState()->willReturn(Argument::not(OrderInterface::STATE_CANCELLED));

        $orderUpdater->update($order)->shouldBeCalled();

        $this->process($order);
    }
}
