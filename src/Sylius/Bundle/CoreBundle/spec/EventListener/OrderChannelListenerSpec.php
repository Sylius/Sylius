<?php

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Manuel Gonalez <mgonyan@gmail.com>
 */
class OrderChannelListenerSpec extends ObjectBehavior
{
    function let(ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($channelContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderChannelListener');
    }

    function it_throws_an_exception_if_event_subject_is_an_invaliad_order_instance(GenericEvent $event)
    {
        $orderClass = new \stdClass();
        $exception = new UnexpectedTypeException(
            $orderClass,
            'Sylius\Component\Core\Model\OrderInterface'
        );

        $event->getSubject()->shouldBeCalled()->willReturn($orderClass);

        $this->shouldThrow($exception)->duringProcessOrderChannel($event);
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
