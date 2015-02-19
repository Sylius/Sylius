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
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface;

/**
 * User register listener spec.
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserUpdateListenerSpec extends ObjectBehavior
{
    function let(UserReloaderInterface $userReloader)
    {
        $this->beConstructedWith($userReloader);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\EventListener\UserUpdateListener');
    }

    function it_updates_user_password($userReloader, GenericEvent $event, UserInterface $user)
    {
        $event->getSubject()->willReturn($user);

        $userReloader->reloadUser($user)->shouldBeCalled();

        $this->processUser($event);
    }
}
