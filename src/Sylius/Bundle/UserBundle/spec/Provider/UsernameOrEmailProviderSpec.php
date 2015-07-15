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
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class UsernameOrEmailProviderSpec extends ObjectBehavior
{
    public function let(UserRepositoryInterface $userRepository, CanonicalizerInterface $canonicalizer)
    {
        $this->beConstructedWith($userRepository, $canonicalizer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider');
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

    public function it_does_not_support_other_classes()
    {
        $this->supportsClass('Sylius\Component\User\Model\GroupInterface')->shouldReturn(false);
        $this->supportsClass('Acme\Fake\Class')->shouldReturn(false);
    }

    public function it_loads_user_by_username($userRepository, $canonicalizer, UserInterface $user)
    {
        $canonicalizer->canonicalize('testUser')->willReturn('testuser');

        $userRepository->findOneBy(array('usernameCanonical' => 'testuser'))->willReturn($user);

        $this->loadUserByUsername('testUser')->shouldReturn($user);
    }

    public function it_throws_exception_when_there_is_no_user_with_given_username_or_email($userRepository, $canonicalizer)
    {
        $canonicalizer->canonicalize('testUser')->willReturn('testuser');

        $userRepository->findOneBy(array('usernameCanonical' => 'testuser'))->willReturn(null);
        $userRepository->findOneByEmail('testuser')->willReturn(null);

        $this->shouldThrow(new UsernameNotFoundException('Username "testuser" does not exist.'))->during('loadUserByUsername', array('testUser'));
    }

    public function it_loads_user_by_email($userRepository, $canonicalizer, UserInterface $user)
    {
        $canonicalizer->canonicalize('test@user.com')->willReturn('test@user.com');

        $userRepository->findOneByEmail('test@user.com')->willReturn($user);

        $this->loadUserByUsername('test@user.com')->shouldReturn($user);
    }

    public function it_refreshes_user($userRepository, UserInterface $user, UserInterface $refreshedUser)
    {
        $userRepository->find(1)->willReturn($refreshedUser);

        $user->getId()->willReturn(1);

        $this->refreshUser($user)->shouldReturn($refreshedUser);
    }
}
