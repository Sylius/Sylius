<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\OAuth;

use Doctrine\Common\Persistence\ObjectManager;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\UserBundle\Doctrine\ORM\CustomerRepository;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\CustomerInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

class UserProviderSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $customerFactory,
        CustomerRepository $customerRepository,
        FactoryInterface $userFactory,
        UserRepositoryInterface $userRepository,
        FactoryInterface $oauthFactory,
        RepositoryInterface $oauthRepository,
        ObjectManager $userManager,
        CanonicalizerInterface $canonicalizer
    ) {
        $this->beConstructedWith($customerFactory, $customerRepository, $userFactory, $userRepository, $oauthFactory, $oauthRepository, $userManager, $canonicalizer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\OAuth\UserProvider');
    }

    function it_implements_Hwi_oauth_aware_user_provider_interface()
    {
        $this->shouldImplement(OAuthAwareUserProviderInterface::class);
    }

    function it_implements_account_connector_interface()
    {
        $this->shouldImplement(AccountConnectorInterface::class);
    }

    function it_should_connect_oauth_account_with_given_user(
        $userManager,
        FactoryInterface $oauthFactory,
        UserInterface $user,
        UserResponseInterface $response,
        ResourceOwnerInterface $resourceOwner,
        UserOAuthInterface $oauth
    ) {
        $resourceOwner->getName()->willReturn('google');

        $response->getEmail()->willReturn(null);
        $response->getUsername()->willReturn('username');
        $response->getResourceOwner()->willReturn($resourceOwner);
        $response->getAccessToken()->willReturn('access_token');

        $oauthFactory->createNew()->willReturn($oauth);

        $oauth->setIdentifier('username');
        $oauth->setProvider('google');
        $oauth->setAccessToken('access_token');

        $user->addOAuthAccount($oauth)->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->connect($user, $response);
    }

    function it_should_return_user_if_relation_exists(
        $oauthRepository,
        UserInterface $user,
        UserOAuthInterface $oauth,
        UserResponseInterface $response,
        ResourceOwnerInterface $resourceOwner
    ) {
        $resourceOwner->getName()->willReturn('google');

        $response->getUsername()->willReturn('username');
        $response->getResourceOwner()->willReturn($resourceOwner);

        $oauthRepository->findOneBy(['provider' => 'google', 'identifier' => 'username'])->willReturn($oauth);
        $oauth->getUser()->willReturn($user);

        $this->loadUserByOAuthUserResponse($response)->shouldReturn($user);
    }

    function it_should_update_user_when_he_was_found_by_email(
        $userManager,
        $userRepository,
        FactoryInterface $oauthFactory,
        RepositoryInterface $oauthRepository,
        UserInterface $user,
        UserResponseInterface $response,
        ResourceOwnerInterface $resourceOwner,
        UserOAuthInterface $oauth
    ) {
        $resourceOwner->getName()->willReturn('google');

        $response->getEmail()->willReturn('username@email');
        $response->getUsername()->willReturn('username');
        $response->getResourceOwner()->willReturn($resourceOwner);
        $response->getAccessToken()->willReturn('access_token');

        $oauthRepository->findOneBy(['provider' => 'google', 'identifier' => 'username'])->willReturn(null);
        $oauthFactory->createNew()->willReturn($oauth);

        $userRepository->findOneByEmail('username@email')->willReturn($user);

        $oauth->setIdentifier('username');
        $oauth->setProvider('google');
        $oauth->setAccessToken('access_token');

        $user->addOAuthAccount($oauth)->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->loadUserByOAuthUserResponse($response)->shouldReturn($user);
    }

    function it_should_create_new_user_when_none_was_found(
        $userManager,
        FactoryInterface $customerFactory,
        FactoryInterface $userFactory,
        FactoryInterface $oauthFactory,
        RepositoryInterface $oauthRepository,
        CustomerInterface $customer,
        UserInterface $user,
        UserResponseInterface $response,
        ResourceOwnerInterface $resourceOwner,
        UserOAuthInterface $oauth
    ) {
        $resourceOwner->getName()->willReturn('google');

        $response->getEmail()->willReturn(null);
        $response->getUsername()->willReturn('username');
        $response->getNickname()->willReturn('user');
        $response->getRealName()->willReturn('Name');
        $response->getResourceOwner()->willReturn($resourceOwner);
        $response->getAccessToken()->willReturn('access_token');

        $oauthRepository->findOneBy(['provider' => 'google', 'identifier' => 'username'])->willReturn(null);
        $oauthFactory->createNew()->willReturn($oauth);

        $userFactory->createNew()->willReturn($user);
        $customerFactory->createNew()->willReturn($customer);
        $customer->setFirstName('Name')->shouldBeCalled();

        $oauth->setIdentifier('username');
        $oauth->setProvider('google');
        $oauth->setAccessToken('access_token');

        $user->setCustomer($customer)->shouldBeCalled();
        $user->getUsername()->willReturn(null);
        $user->setUsername('user')->shouldBeCalled();
        $user->setPlainPassword('2ff2dfe363')->shouldBeCalled();
        $user->setEnabled(true)->shouldBeCalled();
        $user->addOAuthAccount($oauth)->shouldBeCalled();

        $userManager->persist($user)->shouldBeCalled();
        $userManager->flush()->shouldBeCalled();

        $this->loadUserByOAuthUserResponse($response)->shouldReturn($user);
    }
}
