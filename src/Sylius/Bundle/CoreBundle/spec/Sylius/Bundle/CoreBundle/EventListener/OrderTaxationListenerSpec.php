<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\OrderProcessing\TaxationProcessorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderTaxationListenerSpec extends ObjectBehavior
{
    function let(TaxationProcessorInterface $taxationProcessor)
    {
        $this->beConstructedWith($taxationProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderTaxationListener');
    }

    function it_throws_exception_if_event_has_non_order_subject(GenericEvent $event, \stdClass $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringApplyTaxes($event)
        ;
    }

    function it_calls_taxation_processor_on_order(TaxationProcessorInterface $taxationProcessor, GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);
        $taxationProcessor->applyTaxes($order)->shouldBeCalled();
        $order->calculateTotal()->shouldBeCalled();

        $this->applyTaxes($event);
    }
}
