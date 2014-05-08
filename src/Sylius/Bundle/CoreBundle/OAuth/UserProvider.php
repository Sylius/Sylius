<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\OAuth;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\User;
use Sylius\Component\Core\Model\UserOAuth;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 *
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 */
class UserProvider extends FOSUBUserProvider
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     * @param ObjectManager        $om
     * @param EntityRepository     $repository
     */
    public function __construct(UserManagerInterface $userManager, ObjectManager $om, EntityRepository $repository)
    {
        $this->userManager = $userManager;
        $this->om          = $om;
        $this->repository  = $repository;
    }

    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->repository->findOneBy(array(
            'provider'   => $response->getResourceOwner()->getName(),
            'identifier' => $response->getUsername()
        ));

        if (null !== $user) {
            return $user;
        }

        if (null !== $response->getEmail()) {
            $user = $this->userManager->findUserByEmail($response->getEmail());
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
        $this->updateUserByOAuthUserResponse($user, $response);

        $this->userManager->updateUser($user);
    }

    /**
     * Ad-hoc creation of user
     *
     * @param UserResponseInterface $response
     *
     * @return User
     */
    protected function createUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $user = $this->userManager->createUser();

        // set default values taken from OAuth sign-in provider account
        if (null !== $email = $response->getEmail()) {
            $user->setEmail($email);
        }

        // if username was not yet set (i.e. by internal call in `setEmail()`), use nickname
        if (!$user->getUsername()) {
            $user->setUsername($response->getNickname());
        }

        // set random password to prevent issue with not nullable field & potential security hole
        $user->setPlainPassword(substr(sha1($response->getAccessToken()), 0, 10));

        $user->setEnabled(true);

        $this->userManager->updateUser($user);
        $this->updateUserByOAuthUserResponse($user, $response);

        return $user;
    }

    /**
     * Attach OAuth sign-in provider account to existing user
     *
     * @param FOSUserInterface      $user
     * @param UserResponseInterface $response
     *
     * @return FOSUserInterface
     */
    protected function updateUserByOAuthUserResponse(FOSUserInterface $user, UserResponseInterface $response)
    {
        $oauth = new UserOAuth();
        $oauth->setIdentifier($response->getUsername());
        $oauth->setProvider($response->getResourceOwner()->getName());
        $oauth->setAccessToken($response->getAccessToken());
        $oauth->setUser($user);

        $this->om->persist($oauth);
        $this->om->flush();

        return $user;
    }
}
