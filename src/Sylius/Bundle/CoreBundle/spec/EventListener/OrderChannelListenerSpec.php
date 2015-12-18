<?php

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\OrderChannelListener;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Manuel Gonalez <mgonyan@gmail.com>
 */
class OrderChannelListenerSpec extends ObjectBehavior
{
    function let(
        ChannelContextInterface $channelContext
    )
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderChannelListener::class);
    }

    function it_throws_an_exception_if_event_subject_is_an_invaliad_order_instance(
        GenericEvent $event
    )
    {
        $orderClass = new \stdClass();
        $event->getSubject()->shouldBeCalled()->willReturn($orderClass);

        $this->shouldThrow(\UnexpectedValueException::class)->duringProcessOrderChannel($event);
    }

    function it_proccess_order_channel_successfully(
        GenericEvent $event,
        OrderInterface $order,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $event->getSubject()->shouldBeCalled()->willReturn($order);
        $channelContext->getChannel()->shouldBeCalled()->willReturn($channel);
        $order->setChannel($channel)->shouldBeCalled();

        $this->processOrderChannel($event);
    }
}
