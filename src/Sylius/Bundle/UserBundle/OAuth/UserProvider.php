<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\UserBundle\OAuth;

use Doctrine\Common\Persistence\ObjectManager;
use HWI\Bundle\OAuthBundle\Connect\AccountConnectorInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Sylius\Bundle\UserBundle\Provider\UsernameOrEmailProvider as BaseUserProvider;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Sylius\Component\User\Model\UserInterface as SyliusUserInterface;
use Sylius\Component\User\Model\UserOAuthInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 *
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserProvider extends BaseUserProvider implements AccountConnectorInterface, OAuthAwareUserProviderInterface
{
    /**
     * @var ResourceFactoryInterface
     */
    protected $customerFactory;

    /**
     * @var ResourceFactoryInterface
     */
    protected $userFactory;

    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * @var ObjectManager
     */
    protected $userManager;

    /**
     * @var ResourceFactoryInterface
     */
    protected $oauthFactory;

    /**
     * @var ResourceRepositoryInterface
     */
    protected $oauthRepository;

    /**
     * Constructor.
     *
     * @param ResourceFactoryInterface    $customerFactory
     * @param ResourceFactoryInterface    $userFactory
     * @param UserRepositoryInterface     $userRepository
     * @param ResourceManagerInterface    $userManager
     * @param ResourceFactoryInterface    $oauthFactory
     * @param ResourceRepositoryInterface $oauthRepository
     */
    public function __construct(
        ResourceFactoryInterface $customerFactory,
        ResourceFactoryInterface $userFactory,
        UserRepositoryInterface $userRepository,
        ResourceManagerInterface $userManager,
        ResourceFactoryInterface $oauthFactory,
        ResourceRepositoryInterface $oauthRepository,
        CanonicalizerInterface $canonicalizer
    ) {
        parent::__construct($userRepository, $canonicalizer);

        $this->customerFactory = $customerFactory;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->oauthFactory = $oauthFactory;
        $this->oauthRepository = $oauthRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $oauth = $this->oauthRepository->findOneBy(array(
            'provider'   => $response->getResourceOwner()->getName(),
            'identifier' => $response->getUsername(),
        ));

        if ($oauth instanceof UserOAuthInterface) {
            return $oauth->getUser();
        }

        if (null !== $response->getEmail()) {
            $user = $this->userRepository->findOneByEmail($response->getEmail());
            if (null !== $user) {
                return $this->updateUserByOAuthUserResponse($user, $response);
            }
        }

        return $this->createUserByOAuthUserResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        /* @var $user SyliusUserInterface */
        $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Ad-hoc creation of user.
     *
     * @param UserResponseInterface $response
     *
     * @return SyliusUserInterface
     */
    protected function createUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->userFactory->createNew();
        $customer = $this->customerFactory->createNew();

        $user->setCustomer($customer);

        // Set default values taken from OAuth sign-in provider account.
        if (null !== $email = $response->getEmail()) {
            $customer->setEmail($email);
        }

        if (!$user->getUsername()) {
            $user->setUsername($response->getEmail() ?: $response->getNickname());
        }

        // Set random password to prevent issue with not nullable field & potential security hole.
        $user->setPlainPassword(substr(sha1($response->getAccessToken()), 0, 10));

        $user->setEnabled(true);

        return $this->updateUserByOAuthUserResponse($user, $response);
    }

    /**
     * Attach OAuth sign-in provider account to existing user.
     *
     * @param UserInterface         $user
     * @param UserResponseInterface $response
     *
     * @return UserInterface
     */
    protected function updateUserByOAuthUserResponse(UserInterface $user, UserResponseInterface $response)
    {
        $oauth = $this->oauthFactory->createNew();
        $oauth->setIdentifier($response->getUsername());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());

        /* @var $user SyliusUserInterface */
        $user->addOAuthAccount($oauth);

        $this->userManager->persist($user);
        $this->userManager->flush();

        return $user;
    }
}
