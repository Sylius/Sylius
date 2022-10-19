<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Listener\Order\AssignTokenListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\Workflow\Event\Event;

final class AssignTokenListenerSpec extends ObjectBehavior
{
    function let(OrderTokenAssignerInterface $orderTokenAssigner): void
    {
        $this->beConstructedWith($orderTokenAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignTokenListener::class);
    }

    function it_assigns_order_tokens(
        Event $event,
        OrderInterface $order,
        OrderTokenAssignerInterface $orderTokenAssigner,
    ): void {
        $event->getSubject()->willReturn($order);

        $orderTokenAssigner->assignTokenValue($order)->shouldBeCalled();

        $this->assignToken($event);
    }
}
