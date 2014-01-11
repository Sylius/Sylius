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

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderStateListenerSpec extends ObjectBehavior
{
    /**
      @param Sylius\Bundle\CoreBundle\OrderProcessing\StateResolverInterface $stateResolver
     */
    function let($stateResolver)
    {
        $this->beConstructedWith($stateResolver);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderStateListener');
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
            ->duringOnCheckoutFinalizePreComplete($event)
        ;
    }

    /**
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface  $order
     */
    function it_resolves_order_states_before_after_finalizing_the_checkout($stateResolver, $event, $order)
    {
        $event->getSubject()->willReturn($order);
        $stateResolver->resolveShippingState($order)->shouldBeCalled();
        $stateResolver->resolvePaymentState($order)->shouldBeCalled();

        $this->onCheckoutFinalizePreComplete($event);
    }
}
