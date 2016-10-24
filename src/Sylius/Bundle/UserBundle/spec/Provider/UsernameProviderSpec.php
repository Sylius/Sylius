<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Provider\AbstractUserProvider;
use Sylius\Bundle\UserBundle\Provider\UsernameProvider;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class UsernameProviderSpec extends ObjectBehavior
{
    function let(UserRepositoryInterface $userRepository, CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith(User::class, $userRepository, $canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UsernameProvider::class);
    }

    function it_implements_symfony_user_provider_interface()
    {
        $this->shouldImplement(UserProviderInterface::class);
    }

    function it_should_extend_user_provider()
    {
        $this->shouldHaveType(AbstractUserProvider::class);
    }

    function it_supports_sylius_user_model()
    {
        $this->supportsClass(User::class)->shouldReturn(true);
    }

    function it_loads_user_by_user_name(
        UserRepositoryInterface $userRepository,
        CanonicalizerInterface $canonicalizer,
        User $user
    ) {
        $canonicalizer->canonicalize('testUser')->willReturn('testuser');

        $userRepository->findOneBy(['usernameCanonical' => 'testuser'])->willReturn($user);

        $this->loadUserByUsername('testUser')->shouldReturn($user);
    }

    function it_updates_user_by_user_name(UserRepositoryInterface $userRepository, User $user)
    {
        $userRepository->find(1)->willReturn($user);

        $user->getId()->willReturn(1);

        $this->refreshUser($user)->shouldReturn($user);
    }
}
