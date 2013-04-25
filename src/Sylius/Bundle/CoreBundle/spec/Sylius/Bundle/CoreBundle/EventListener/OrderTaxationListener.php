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

use PHPSpec2\ObjectBehavior;

/**
 * Order taxation listener spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderTaxationListener extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\CoreBundle\OrderProcessing\TaxationProcessorInterface $taxationProcessor
     */
    function let($taxationProcessor)
    {
        $this->beConstructedWith($taxationProcessor);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderTaxationListener');
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param \stdClass                                      $invalidSubject
     */
    function it_throws_exception_if_event_has_non_order_subject($event, $invalidSubject)
    {
        $event->getSubject()->willReturn($invalidSubject);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringProcessOrderTaxation($event)
        ;
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface  $order
     */
    function it_calls_taxation_processor_on_order($taxationProcessor, $event, $order)
    {
        $event->getSubject()->willReturn($order);
        $taxationProcessor->applyTaxes($order)->shouldBeCalled();

        $this->processOrderTaxation($event);
    }
}
