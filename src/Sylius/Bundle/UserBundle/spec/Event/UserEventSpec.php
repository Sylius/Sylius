<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Event;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\UserInterface;

class UserEventSpec extends ObjectBehavior
{
    function let(UserInterface $user)
    {
        $this->beConstructedWith($user);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Event\UserEvent');
    }

    function it_has_user($user)
    {
        $this->getUser()->shouldReturn($user);
    }
}
