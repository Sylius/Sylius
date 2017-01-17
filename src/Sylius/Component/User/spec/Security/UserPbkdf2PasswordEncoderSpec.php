<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Security;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;
use Sylius\Component\User\Security\UserPbkdf2PasswordEncoder;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class UserPbkdf2PasswordEncoderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UserPbkdf2PasswordEncoder::class);
    }

    function it_implements_password_updater_interface()
    {
        $this->shouldImplement(UserPasswordEncoderInterface::class);
    }

    function it_encodes_password(CredentialsHolderInterface $user)
    {
        $user->getPlainPassword()->willReturn('myPassword');
        $user->getSalt()->willReturn('typicalSalt');

        $this->encode($user)->shouldReturn('G1DuArwJiu+4Ctk9p2965gC3SXjGcom6gNhmV0OGUm79Kb9Anm5GWg==');
    }
}
