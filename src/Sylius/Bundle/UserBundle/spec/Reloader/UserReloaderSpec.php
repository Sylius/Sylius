<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Reloader;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\User\Model\UserInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserReloaderSpec extends ObjectBehavior
{
    function let(ObjectManager $objectManager)
    {
        $this->beConstructedWith($objectManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Reloader\UserReloader');
    }

    function it_implements_user_reloader_interface()
    {
        $this->shouldImplement('Sylius\Bundle\UserBundle\Reloader\UserReloaderInterface');
    }

    function it_reloads_user($objectManager, UserInterface $user)
    {
        $objectManager->refresh($user)->shouldBeCalled();

        $this->reloadUser($user);
    }
}
