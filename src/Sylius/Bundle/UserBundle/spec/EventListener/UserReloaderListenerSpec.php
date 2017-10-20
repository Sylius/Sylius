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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class UserReloaderListenerSpec extends ObjectBehavior
{
    function let(UserReloaderInterface $userReloader): void
    {
        $this->beConstructedWith($userReloader);
    }

    function it_reloads_user(UserReloaderInterface $userReloader, GenericEvent $event, UserInterface $user): void
    {
        $event->getSubject()->willReturn($user);

        $userReloader->reloadUser($user)->shouldBeCalled();

        $this->reloadUser($event);
    }

    function it_throws_exception_when_reloading_not_a_user_interface(
        UserReloaderInterface $userReloader,
        GenericEvent $event
    ): void {
        $event->getSubject()->willReturn('user');

        $userReloader->reloadUser(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('reloadUser', [$event])
        ;
    }
}
