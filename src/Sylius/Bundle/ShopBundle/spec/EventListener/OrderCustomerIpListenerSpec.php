<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class OrderCustomerIpListenerSpec extends ObjectBehavior
{
    function let(IpAssignerInterface $ipAssigner, RequestStack $requestStack): void
    {
        $this->beConstructedWith($ipAssigner, $requestStack);
    }

    function it_uses_assigner_to_assign_customer_ip_to_order(
        GenericEvent $event,
        IpAssignerInterface $ipAssigner,
        OrderInterface $order,
        Request $request,
        RequestStack $requestStack
    ): void {
        $event->getSubject()->willReturn($order);
        $requestStack->getMasterRequest()->willReturn($request);

        $ipAssigner->assign($order, $request)->shouldBeCalled();

        $this->assignCustomerIpToOrder($event);
    }

    function it_throws_exception_if_event_subject_is_not_order(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('assignCustomerIpToOrder', [$event])
        ;
    }
}
