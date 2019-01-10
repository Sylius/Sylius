<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\spec\Fixture\FixtureUser;
use Sylius\Bundle\UserBundle\spec\Fixture\FixtureUserInterface;
use Sylius\Component\User\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class UpdateUserEncoderListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $objectManager): void
    {
        $this->beConstructedWith($objectManager, 'newalgo', FixtureUser::class, FixtureUserInterface::class, '_password');
    }

    function it_does_nothing_if_user_does_not_implement_user_interface(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $event->getAuthenticationToken()->willReturn($token);

        $user = new \stdClass();

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin($event);
    }

    function it_does_nothing_if_user_does_not_implement_specified_class_or_interface(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $event->getAuthenticationToken()->willReturn($token);

        $user = new User();

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin($event);
    }

    function it_does_nothing_if_user_uses_the_recommended_encoder(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $event->getAuthenticationToken()->willReturn($token);

        $user = new FixtureUser();
        $user->setEncoderName('newalgo');

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin($event);

        assert($user->getEncoderName() === 'newalgo');
        assert($user->getPlainPassword() === null);
    }

    function it_does_nothing_if_plain_password_could_not_be_resolved(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $request = new Request();

        $event->getAuthenticationToken()->willReturn($token);
        $event->getRequest()->willReturn($request);

        $user = new FixtureUser();
        $user->setEncoderName('oldalgo');

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin($event);

        assert($user->getEncoderName() === 'oldalgo');
        assert($user->getPlainPassword() === null);
    }

    function it_does_nothing_if_resolved_plain_password_is_null(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $request = new Request();
        $request->request->set('_password', null);

        $event->getAuthenticationToken()->willReturn($token);
        $event->getRequest()->willReturn($request);

        $user = new FixtureUser();
        $user->setEncoderName('oldalgo');

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin($event);

        assert($user->getEncoderName() === 'oldalgo');
        assert($user->getPlainPassword() === null);
    }

    function it_does_nothing_if_resolved_plain_password_is_empty(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $request = new Request();
        $request->request->set('_password', '');

        $event->getAuthenticationToken()->willReturn($token);
        $event->getRequest()->willReturn($request);

        $user = new FixtureUser();
        $user->setEncoderName('oldalgo');

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin($event);

        assert($user->getEncoderName() === 'oldalgo');
        assert($user->getPlainPassword() === null);
    }

    function it_updates_the_encoder_and_plain_password_if_using_old_encoder_and_plain_password_could_be_resolved(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $request = new Request();
        $request->request->set('_password', 'plainpassword');

        $event->getAuthenticationToken()->willReturn($token);
        $event->getRequest()->willReturn($request);

        $user = new FixtureUser();
        $user->setEncoderName('oldalgo');

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin($event);

        assert($user->getEncoderName() === 'newalgo');
        assert($user->getPlainPassword() === 'plainpassword');
    }

    function it_updates_the_encoder_and_plain_password_if_using_default_null_encoder_and_plain_password_could_be_resolved(
        ObjectManager $objectManager,
        InteractiveLoginEvent $event,
        TokenInterface $token
    ): void {
        $request = new Request();
        $request->request->set('_password', 'plainpassword');

        $event->getAuthenticationToken()->willReturn($token);
        $event->getRequest()->willReturn($request);

        $user = new FixtureUser();
        $user->setEncoderName(null);

        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin($event);

        assert($user->getEncoderName() === 'newalgo');
        assert($user->getPlainPassword() === 'plainpassword');
    }
}
