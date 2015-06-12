<?php

namespace spec\Sylius\Bundle\UserBundle\Event;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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
