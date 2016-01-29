<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\User');
    }

    function it_implements_user_interface()
    {
        $this->shouldImplement(UserInterface::class);
    }

    function it_has_no_password_requested_at_date_by_default()
    {
        $this->getPasswordRequestedAt()->shouldReturn(null);
    }

    function its_password_requested_at_date_is_mutable()
    {
        $date = new \DateTime();
        $this->setPasswordRequestedAt($date);

        $this->getPasswordRequestedAt()->shouldReturn($date);
    }

    function it_should_return_true_if_password_request_is_non_expired()
    {
        $passwordRequestedAt = new \DateTime('-1 hour');
        $this->setPasswordRequestedAt($passwordRequestedAt);
        $ttl = new \DateInterval('P1D');

        $this->isPasswordRequestNonExpired($ttl)->shouldReturn(true);
    }

    function it_should_return_false_if_password_request_is_expired()
    {
        $passwordRequestedAt = new \DateTime('-2 hour');
        $this->setPasswordRequestedAt($passwordRequestedAt);
        $ttl = new \DateInterval('PT1H');

        $this->isPasswordRequestNonExpired($ttl)->shouldReturn(false);
    }
}
