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
use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReloaderListenerSpec extends ObjectBehavior
{
    function let(UserReloaderInterface $userReloader)
    {
        $this->beConstructedWith($userReloader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserReloaderListener');
    }

    function it_reloads_user($userReloader, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $userReloader->reloadUser($user)->shouldBeCalled();

        $this->reloadUser($event);
    }

    function it_throw_exception_for_other_implementations_then_user_interface($userReloader, GenericEvent $event, UserInterface $user)
    {
        $user = '';
        $event->getSubject()->willReturn($user);
        $this->shouldThrow(new UnexpectedTypeException($user, UserInterface::class))
            ->duringReloadUser($event);
    }
}
