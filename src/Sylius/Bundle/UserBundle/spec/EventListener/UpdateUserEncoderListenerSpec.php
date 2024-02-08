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

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\spec\Fixture\FixtureUser;
use Sylius\Bundle\UserBundle\spec\Fixture\FixtureUserInterface;
use Sylius\Component\User\Model\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

final class UpdateUserEncoderListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $objectManager): void
    {
        $this->beConstructedWith($objectManager, 'newalgo', FixtureUser::class, FixtureUserInterface::class, '_password');
    }

    function it_does_nothing_if_user_does_not_implement_user_interface(
        ObjectManager $objectManager,
        Request $request,
        TokenInterface $token,
        UserInterface $user,
    ): void {
        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_user_does_not_implement_specified_class_or_interface(
        ObjectManager $objectManager,
        Request $request,
        TokenInterface $token,
        User $user,
    ): void {
        $token->getUser()->willReturn($user);

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_user_uses_the_recommended_encoder(
        ObjectManager $objectManager,
        Request $request,
        TokenInterface $token,
        FixtureUser $user,
    ): void {
        $token->getUser()->willReturn($user);

        $user->getEncoderName()->willReturn('newalgo');

        $user->setEncoderName(Argument::any())->shouldNotBeCalled();
        $user->setPlainPassword(Argument::any())->shouldNotBeCalled();

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request->getWrappedObject(), $token->getWrappedObject()));
    }

    function it_does_nothing_if_plain_password_could_not_be_resolved(
        ObjectManager $objectManager,
        TokenInterface $token,
        FixtureUser $user,
    ): void {
        $request = new Request();

        $token->getUser()->willReturn($user);

        $user->getEncoderName()->willReturn('oldalgo');

        $user->setEncoderName(Argument::any())->shouldNotBeCalled();
        $user->setPlainPassword(Argument::any())->shouldNotBeCalled();

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token->getWrappedObject()));
    }

    function it_does_nothing_if_resolved_plain_password_is_null(
        ObjectManager $objectManager,
        TokenInterface $token,
        FixtureUser $user,
    ): void {
        $request = new Request();
        $request->request->set('_password', null);

        $token->getUser()->willReturn($user);

        $user->getEncoderName()->willReturn('oldalgo');

        $user->setEncoderName(Argument::any())->shouldNotBeCalled();
        $user->setPlainPassword(Argument::any())->shouldNotBeCalled();

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token->getWrappedObject()));
    }

    function it_does_nothing_if_resolved_plain_password_is_empty(
        ObjectManager $objectManager,
        TokenInterface $token,
        FixtureUser $user,
    ): void {
        $request = new Request();
        $request->request->set('_password', '');

        $token->getUser()->willReturn($user);

        $user->getEncoderName()->willReturn('oldalgo');

        $user->setEncoderName(Argument::any())->shouldNotBeCalled();
        $user->setPlainPassword(Argument::any())->shouldNotBeCalled();

        $objectManager->persist($user)->shouldNotBeCalled();
        $objectManager->flush()->shouldNotBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token->getWrappedObject()));
    }

    function it_updates_the_encoder_and_plain_password_if_using_old_encoder_and_plain_password_could_be_resolved(
        ObjectManager $objectManager,
        TokenInterface $token,
        FixtureUser $user,
    ): void {
        $request = new Request();
        $request->request->set('_password', 'plainpassword');

        $token->getUser()->willReturn($user);

        $user->getEncoderName()->willReturn('oldalgo');

        $user->setEncoderName('newalgo')->shouldBeCalled();
        $user->setPlainPassword('plainpassword')->shouldBeCalled();

        $objectManager->persist($user)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token->getWrappedObject()));
    }

    function it_updates_the_encoder_and_plain_password_if_using_default_null_encoder_and_plain_password_could_be_resolved(
        ObjectManager $objectManager,
        TokenInterface $token,
        FixtureUser $user,
    ): void {
        $request = new Request();
        $request->request->set('_password', 'plainpassword');

        $token->getUser()->willReturn($user);

        $user->getEncoderName()->willReturn(null);

        $user->setEncoderName('newalgo')->shouldBeCalled();
        $user->setPlainPassword('plainpassword')->shouldBeCalled();

        $objectManager->persist($user)->shouldBeCalled();
        $objectManager->flush()->shouldBeCalled();

        $this->onSecurityInteractiveLogin(new InteractiveLoginEvent($request, $token->getWrappedObject()));
    }
}
