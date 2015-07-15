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
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\User;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UsernameProviderSpec extends ObjectBehavior
{
    public function let(UserRepositoryInterface $userRepository, CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith($userRepository, $canonicalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Provider\UsernameProvider');
    }

    public function it_implements_symfony_user_provider_interface()
    {
        $this->shouldImplement('Symfony\Component\Security\Core\User\UserProviderInterface');
    }

    public function it_should_extend_user_provider()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Provider\AbstractUserProvider');
    }

    public function it_supports_sylius_user_model()
    {
        $this->supportsClass('Sylius\Component\User\Model\UserInterface')->shouldReturn(true);
    }

    public function it_loads_user_by_user_name($userRepository, $canonicalizer, User $user)
    {
        $canonicalizer->canonicalize('testUser')->willReturn('testuser');

        $userRepository->findOneBy(array('usernameCanonical' => 'testuser'))->willReturn($user);

        $this->loadUserByUsername('testUser')->shouldReturn($user);
    }

    public function it_updates_user_by_user_name($userRepository, User $user)
    {
        $userRepository->find(1)->willReturn($user);

        $user->getId()->willReturn(1);

        $this->refreshUser($user)->shouldReturn($user);
    }
}
