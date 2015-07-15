<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\UserEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLastLoginSubscriberSpec extends ObjectBehavior
{
    public function let(ObjectManager $userManager)
    {
        $this->beConstructedWith($userManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber');
    }

    public function it_is_subscriber()
    {
        $this->shouldImplement('Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber');
    }

    public function it_subscriber_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
        ));
    }

    public function it_updates_user_last_login_on_security_interactive_login(
        InteractiveLoginEvent $event,
        TokenInterface $token,
        UserInterface $user,
        $userManager
    ) {
        $event->getAuthenticationToken()->shouldBeCalled()->willReturn($token);
        $token->getUser()->shouldBeCalled()->willReturn($user);

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush($user)->shouldBeCalled();

        $this->onSecurityInteractiveLogin($event);
    }

    public function it_updates_user_last_login_on_implicit_login(UserEvent $event, UserInterface $user, $userManager)
    {
        $event->getUser()->shouldBeCalled()->willReturn($user);

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush($user)->shouldBeCalled();

        $this->onImplicitLogin($event);
    }
}
