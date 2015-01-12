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
use FOS\UserBundle\Model\GroupInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sylius\Component\Resource\Event\ResourceEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * User delete listener spec.
 *
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UserDeleteListenerSpec extends ObjectBehavior
{
    function let(SecurityContext $securityContext, UrlGeneratorInterface $router, GroupInterface $groupInterface1, GroupInterface $groupInterface2, SessionInterface $session, FlashBagInterface $flashBag, ResourceEvent $event)
    {
        $this->beConstructedWith($securityContext, $router, $session, 'sylius_backend_user_index');
        $session->getBag('flashes')->willReturn($flashBag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\UserDeleteListener');
    }

    function it_delete_user($event, $groupInterface1, $groupInterface2, $flashBag, $securityContext, TokenInterface $tokenInterface)
    {
        $event->getSubject()->willReturn($groupInterface1);
        $groupInterface1->getId()->willReturn(1);
        $securityContext->getToken()->willReturn($tokenInterface);
        $tokenInterface->getUser()->willReturn($groupInterface2);
        $groupInterface2->getId()->willReturn(2);

        $event->stopPropagation()->shouldNotBeCalled();
        $flashBag->add('error', 'Cannot remove currently logged user.')->shouldNotBeCalled();
        $this->deleteUser($event);
    }

    function it_does_not_allow_delete_currently_logged_user($event, $groupInterface1, $groupInterface2, $securityContext, $flashBag, TokenInterface $tokenInterface)
    {
        $event->getSubject()->willReturn($groupInterface1);
        $groupInterface1->getId()->willReturn(1);
        $securityContext->getToken()->willReturn($tokenInterface);
        $tokenInterface->getUser()->willReturn($groupInterface2);
        $groupInterface2->getId()->willReturn(1);

        $event->stopPropagation()->shouldBeCalled();
        $flashBag->add('error', 'Cannot remove currently logged user.')->shouldBeCalled();
        $this->deleteUser($event);
    }

    function it_throws_exception_if_event_subject_does_not_implement_group_interface($event, TokenInterface $tokenInterface)
    {
        $event->getSubject()->willReturn($tokenInterface);
        $this->shouldThrow('Sylius\Component\Resource\Exception\UnexpectedTypeException')->duringDeleteUser($event);
    }
}
