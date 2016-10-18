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

use Doctrine\ORM\Event\LifecycleEventArgs;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\EventListener\PasswordUpdaterListener;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PasswordUpdaterListenerSpec extends ObjectBehavior
{
    function let(PasswordUpdaterInterface $passwordUpdater)
    {
        $this->beConstructedWith($passwordUpdater);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PasswordUpdaterListener::class);
    }

    function it_updates_password_for_generic_event(
        PasswordUpdaterInterface $passwordUpdater,
        GenericEvent $event,
        UserInterface $user
    ) {
        $event->getSubject()->willReturn($user);

        $user->getPlainPassword()->willReturn('testPassword');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->genericEventUpdater($event);
    }

    function it_allows_to_update_password_for_generic_event_for_user_interface_implementation_only(GenericEvent $event)
    {
        $event->getSubject()->willReturn('user');

        $this
            ->shouldThrow(new UnexpectedTypeException('user', UserInterface::class))
            ->during('genericEventUpdater', [$event])
        ;
    }

    function it_updates_password_on_pre_persist_doctrine_event(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event,
        UserInterface $user
    ) {
        $event->getEntity()->willReturn($user);

        $user->getPlainPassword()->willReturn('testPassword');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->prePersist($event);
    }

    function it_updates_password_on_pre_update_doctrine_event(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event,
        UserInterface $user
    ) {
        $event->getEntity()->willReturn($user);

        $user->getPlainPassword()->willReturn('testPassword');

        $passwordUpdater->updatePassword($user)->shouldBeCalled();

        $this->preUpdate($event);
    }

    function it_updates_password_on_pre_persist_doctrine_event_for_user_interface_implementation_only(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event)
    {
        $event->getEntity()->willReturn('user');
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $this->prePersist($event);
    }

    function it_updates_password_on_pre_update_doctrine_event_for_user_interface_implementation_only(
        PasswordUpdaterInterface $passwordUpdater,
        LifecycleEventArgs $event
    ) {
        $event->getEntity()->willReturn('user');
        $passwordUpdater->updatePassword(Argument::any())->shouldNotBeCalled();

        $this->preUpdate($event);
    }
}
