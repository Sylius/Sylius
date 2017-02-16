<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShopBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Assigner\IpAssignerInterface;
use Sylius\Bundle\ShopBundle\EventListener\OrderCustomerIpListener;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderCustomerIpListenerSpec extends ObjectBehavior
{
    function let(IpAssignerInterface $ipAssigner, RequestStack $requestStack)
    {
        $this->beConstructedWith($ipAssigner, $requestStack);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderCustomerIpListener::class);
    }

    function it_uses_assigner_to_assign_customer_ip_to_order(
        GenericEvent $event,
        IpAssignerInterface $ipAssigner,
        OrderInterface $order,
        Request $request,
        RequestStack $requestStack
    ) {
        $event->getSubject()->willReturn($order);
        $requestStack->getMasterRequest()->willReturn($request);

        $ipAssigner->assign($order, $request)->shouldBeCalled();

        $this->assignCustomerIpToOrder($event);
    }

    function it_throws_exception_if_event_subject_is_not_order(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('assignCustomerIpToOrder', [$event])
        ;
    }
}
