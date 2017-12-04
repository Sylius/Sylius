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

namespace spec\Sylius\Component\User\Security;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\CredentialsHolderInterface;
use Sylius\Component\User\Security\UserPasswordEncoderInterface;

final class UserPbkdf2PasswordEncoderSpec extends ObjectBehavior
{
    function it_implements_password_updater_interface(): void
    {
        $this->shouldImplement(UserPasswordEncoderInterface::class);
    }

    function it_encodes_password(CredentialsHolderInterface $user): void
    {
        $user->getPlainPassword()->willReturn('myPassword');
        $user->getSalt()->willReturn('typicalSalt');

        $this->encode($user)->shouldReturn('G1DuArwJiu+4Ctk9p2965gC3SXjGcom6gNhmV0OGUm79Kb9Anm5GWg==');
    }
}
