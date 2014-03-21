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
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class OrderUserListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\OrderUserListener');
    }

    function let(SecurityContextInterface $securityContext)
    {
        $this->beConstructedWith($securityContext);
    }

    function it_throws_exception_when_object_is_not_user(GenericEvent $event, \stdClass $object)
    {
        $event->getSubject()->willReturn($object);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringSetOrderUser($event)
        ;
    }

    function it_does_nothing_when_context_doesnt_have_user(GenericEvent $event, OrderInterface $order)
    {
        $event->getSubject()->willReturn($order);

        $order->setUser(Argument::any())->shouldNotBeCalled();

        $this->setOrderUser($event);
    }

    function it_sets_user_on_order(
        SecurityContextInterface $securityContext,
        GenericEvent $event,
        OrderInterface $order,
        TokenInterface $token,
        UserInterface $user
    )
    {
        $event->getSubject()->willReturn($order);

        $securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')->willReturn(true);
        $securityContext->getToken()->willReturn($token);

        $token->getUser()->willReturn($user);

        $order->setUser($user)->shouldBeCalled();

        $this->setOrderUser($event);
    }
}
