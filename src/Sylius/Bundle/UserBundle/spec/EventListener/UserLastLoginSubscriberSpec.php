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
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Event\UserEvent;
use Sylius\Bundle\UserBundle\EventListener\UserLastLoginSubscriber;
use Sylius\Bundle\UserBundle\UserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;
use Sylius\Component\User\Model\UserInterface;

final class UserLastLoginSubscriberSpec extends ObjectBehavior
{
    function let(ObjectManager $userManager)
    {
        $this->beConstructedWith($userManager, 'Sylius\Component\User\Model\UserInterface');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserLastLoginSubscriber::class);
    }

    function it_is_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function its_subscribed_to_events()
    {
        $this::getSubscribedEvents()->shouldReturn([
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
            UserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
        ]);
    }

    function it_updates_user_last_login_on_security_interactive_login(
        ObjectManager $userManager,
        InteractiveLoginEvent $event,
        TokenInterface $token,
        UserInterface $user
    ) {
        $event->getAuthenticationToken()->willReturn($token);
        $token->getUser()->willReturn($user);

        $user->setLastLogin(Argument::type(\DateTime::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin($event);
    }

    function it_updates_user_last_login_on_implicit_login(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user
    ) {
        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::type(\DateTime::class))->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->onImplicitLogin($event);
    }

    function it_updates_only_user_specified_in_constructor(
        ObjectManager $userManager,
        UserEvent $event,
        UserInterface $user
    ) {
        $this->beConstructedWith($userManager, 'FakeBundle\User\Model\User');

        $event->getUser()->willReturn($user);

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();
        $userManager->persist(Argument::any())->shouldNotBeCalled();
        $userManager->flush()->shouldNotBeCalled();

        $this->onImplicitLogin($event);
    }
}
