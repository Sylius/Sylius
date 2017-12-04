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

namespace spec\Sylius\Bundle\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class PasswordUpdaterListenerSpec extends ObjectBehavior
{
    function let(PasswordUpdaterInterface $passwordUpdater): void
    {
        $this->beConstructedWith($passwordUpdater);
    }

    function it_updates_password_for_generic_event(
        PasswordUpdaterInterface $passwordUpdater,
        GenericEvent $event,
        UserInterface $user
    ): void {
        $event->getSubject()->willReturn($user);

        $user->getPlainPassword()->willReturn('testPassword');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->genericEventUpdater($event);
    }

    function it_allows_to_update_password_for_generic_event_for_user_interface_implementation_only(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('user');

        $this
            ->shouldThrow(\TypeError::class)
            ->during('genericEventUpdater', [$event])
        ;
    }

    function it_updates_password_on_pre_persist_doctrine_event(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event,
        UserInterface $user
    ): void {
        $event->getEntity()->willReturn($user);

        $user->getPlainPassword()->willReturn('testPassword');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->prePersist($event);
    }

    function it_updates_password_on_pre_update_doctrine_event(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event,
        UserInterface $user
    ): void {
        $event->getEntity()->willReturn($user);

        $user->getPlainPassword()->willReturn('testPassword');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->preUpdate($event);
    }

    function it_updates_password_on_pre_persist_doctrine_event_for_user_interface_implementation_only(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event): void
    {
        $event->getEntity()->willReturn('user');
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $this->prePersist($event);
    }

    function it_updates_password_on_pre_update_doctrine_event_for_user_interface_implementation_only(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event
    ): void {
        $event->getEntity()->willReturn('user');
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $this->preUpdate($event);
    }
}
