<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OAuth;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\ResourceOwnerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Model\UserOAuthInterface;

class UserProviderSpec extends ObjectBehavior
{
    function let(UserManagerInterface $userManager, EntityRepository $oauthRepository)
    {
        $this->beConstructedWith($userManager, $oauthRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OAuth\UserProvider');
    }

    function it_implements_Hwi_oauth_aware_user_provider_interface()
    {
        $this->shouldImplement('HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface');
    }

    function it_should_connect_oauth_account_with_given_user(
        $userManager,
        $oauthRepository,
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

        $oauthRepository->createNew()->willReturn($oauth);

        $oauth->setIdentifier('username');
        $oauth->setProvider('google');
        $oauth->setAccessToken('access_token');

        $user->addOAuthAccount($oauth)->shouldBeCalled();

        $userManager->updateUser($user)->shouldBeCalled();

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

        $oauthRepository->findOneBy(array('provider' => 'google', 'identifier' => 'username'))->willReturn($oauth);
        $oauth->getUser()->willReturn($user);

        $this->loadUserByOAuthUserResponse($response)->shouldReturn($user);
    }

    function it_should_update_user_when_he_was_found_by_email(
        $userManager,
        $oauthRepository,
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

        $oauthRepository->findOneBy(array('provider' => 'google', 'identifier' => 'username'))->willReturn(null);
        $oauthRepository->createNew()->willReturn($oauth);

        $userManager->findUserByEmail('username@email')->willReturn($user);

        $oauth->setIdentifier('username');
        $oauth->setProvider('google');
        $oauth->setAccessToken('access_token');

        $user->addOAuthAccount($oauth)->shouldBeCalled();

        $userManager->updateUser($user)->shouldBeCalled();

        $this->loadUserByOAuthUserResponse($response)->shouldReturn($user);
    }

    function it_should_create_new_user_when_none_was_found(
        $userManager,
        $oauthRepository,
        UserInterface $user,
        UserResponseInterface $response,
        ResourceOwnerInterface $resourceOwner,
        UserOAuthInterface $oauth
    ) {
        $resourceOwner->getName()->willReturn('google');

        $response->getEmail()->willReturn(null);
        $response->getUsername()->willReturn('username');
        $response->getNickname()->willReturn('user');
        $response->getResourceOwner()->willReturn($resourceOwner);
        $response->getAccessToken()->willReturn('access_token');

        $oauthRepository->findOneBy(array('provider' => 'google', 'identifier' => 'username'))->willReturn(null);
        $oauthRepository->createNew()->willReturn($oauth);

        $userManager->createUser()->willReturn($user);

        $oauth->setIdentifier('username');
        $oauth->setProvider('google');
        $oauth->setAccessToken('access_token');

        $user->getUsername()->willReturn(null);
        $user->setUsername('user')->shouldBeCalled();
        $user->setPlainPassword('2ff2dfe363')->shouldBeCalled();
        $user->setEnabled(true)->shouldBeCalled();
        $user->addOAuthAccount($oauth)->shouldBeCalled();

        $userManager->updateUser($user)->shouldBeCalled();

        $this->loadUserByOAuthUserResponse($response)->shouldReturn($user);
    }
}
