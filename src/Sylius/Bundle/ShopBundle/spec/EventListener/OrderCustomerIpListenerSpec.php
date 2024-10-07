<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Workflow\Event\Event;

final class OrderCustomerIpListenerSpec extends ObjectBehavior
{
    function let(IpAssignerInterface $ipAssigner, RequestStack $requestStack): void
    {
        $this->beConstructedWith($ipAssigner, $requestStack);
    }

    function it_uses_assigner_to_assign_customer_ip_to_order(
        Event $event,
        IpAssignerInterface $ipAssigner,
        OrderInterface $order,
        Request $request,
        RequestStack $requestStack,
    ): void {
        $event->getSubject()->willReturn($order);
        $requestStack->getMainRequest()->willReturn($request);

        $ipAssigner->assign($order, $request)->shouldBeCalled();

        $this($event);
    }

    function it_throws_exception_if_event_subject_is_not_order(Event $event, \stdClass $order): void
    {
        $event->getSubject()->willReturn($order);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$event])
        ;
    }

    function it_does_nothing_if_request_is_not_available(
        IpAssignerInterface $ipAssigner,
        OrderInterface $order,
        Event $event,
        RequestStack $requestStack,
        Request $request,
    ): void {
        $event->getSubject()->willReturn($order);
        $requestStack->getMainRequest()->willReturn(null);

        $ipAssigner->assign($order, $request)->shouldNotBeCalled();

        $this($event);
    }
}
