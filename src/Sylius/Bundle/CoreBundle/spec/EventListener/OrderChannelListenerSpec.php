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
            OrderInterface::class
        );

        $event->getSubject()->shouldBeCalled()->willReturn($orderClass);

        $this->shouldThrow($exception)->duringProcessOrderChannel($event);
    }

    function it_processes_order_channel_successfully(
        GenericEvent $event,
        OrderInterface $order,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel
    ) {
        $event->getSubject()->willReturn($order);
        $order->getChannel()->willReturn(null);

        $channelContext->getChannel()->shouldBeCalled()->willReturn($channel);

        $order->setChannel($channel)->shouldBeCalled();

        $this->processOrderChannel($event);
    }

    function it_does_not_proceed_order_channel_if_it_is_already_set(
        GenericEvent $event,
        OrderInterface $order,
        ChannelInterface $channel
    ) {
        $event->getSubject()->willReturn($order);
        $order->getChannel()->willReturn($channel);

        $order->setChannel($channel)->shouldNotBeCalled();

        $this->processOrderChannel($event);
    }
}
