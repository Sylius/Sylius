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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\EventListener\UserReloaderListener;
use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class UserReloaderListenerSpec extends ObjectBehavior
{
    function let(UserReloaderInterface $userReloader)
    {
        $this->beConstructedWith($userReloader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserReloaderListener::class);
    }

    function it_reloads_user(UserReloaderInterface $userReloader, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $userReloader->reloadUser($user)->shouldBeCalled();

        $this->reloadUser($event);
    }

    function it_throws_exception_when_reloading_not_a_user_interface(
        UserReloaderInterface $userReloader,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn('user');

        $userReloader->reloadUser(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(new UnexpectedTypeException('user', UserInterface::class))
            ->during('reloadUser', [$event])
        ;
    }
}
