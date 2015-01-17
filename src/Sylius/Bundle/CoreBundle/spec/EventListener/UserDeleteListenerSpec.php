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
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\UserBundle\Model\UserInterface;
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
    function let(SecurityContext $securityContext, UrlGeneratorInterface $router, UserInterface $userInterface, SessionInterface $session, FlashBagInterface $flashBag, ResourceEvent $event)
    {
        $this->beConstructedWith($securityContext, $router, $session, 'sylius_backend_user_index');
        $session->getBag('flashes')->willReturn($flashBag);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\UserDeleteListener');
    }

    function it_delete_user($event, $userInterface, $flashBag, $securityContext, TokenInterface $tokenInterface)
    {
        $event->getSubject()->willReturn($userInterface);
        $userInterface->getUsernameCanonical()->willReturn("sylius@example.com");
        $securityContext->getToken()->willReturn($tokenInterface);
        $tokenInterface->getUsername()->willReturn("sylius2@example.com");

        $event->stopPropagation()->shouldNotBeCalled();
        $flashBag->add('error', 'Cannot remove currently logged user.')->shouldNotBeCalled();
        $this->deleteUser($event);
    }

    function it_does_not_allow_delete_currently_logged_user($event, $userInterface, $securityContext, $flashBag, TokenInterface $tokenInterface)
    {
        $userInterface->getUsernameCanonical()->willReturn("sylius@example.com");
        $event->getSubject()->willReturn($userInterface);
        $tokenInterface->getUsername()->willReturn("sylius@example.com");
        $securityContext->getToken()->willReturn($tokenInterface);
        $event->stopPropagation()->shouldBeCalled();
        $flashBag->add('error', 'Cannot remove currently logged user.')->shouldBeCalled();
        $this->deleteUser($event);
    }
}
