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
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Model\UserInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Bartosz Siejka <bartosz.siejka@lakion.com>
 */
final class UserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(User::class);
    }

    function it_implements_user_interface()
    {
        $this->shouldImplement(UserInterface::class);
    }

    function its_not_verified_by_default()
    {
        $this->isVerified()->shouldReturn(false);
    }

    function its_verified_at_date_is_mutable(\DateTime $date)
    {
        $this->setVerifiedAt($date);

        $this->getVerifiedAt()->shouldReturn($date);
    }

    function its_verified_when_verified_at_is_not_empty(\DateTime $date)
    {
        $this->setVerifiedAt($date);

        $this->isVerified()->shouldReturn(true);
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

    function it_has_email_and_email_canonical()
    {
        $this->setEmail('admin@example.com');
        $this->setEmailCanonical('user@example.com');

        $this->getEmail()->shouldReturn('admin@example.com');
        $this->getEmailCanonical()->shouldReturn('user@example.com');
    }
}
