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

use FOS\UserBundle\Model\UserInterface as FOSUserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Sylius\Component\Core\Model\User;
use Sylius\Component\Core\Model\UserOauth;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use \Doctrine\ORM\EntityManager;

/**
 * Loading and ad-hoc creation of a user by an OAuth sign-in provider account.
 *
 * @author Fabian Kiss <fabian.kiss@ymc.ch>
 */
class UserProvider extends FOSUBUserProvider
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager FOSUB user provider.
     *
     */
    public function __construct( UserManagerInterface $userManager, EntityManager $em )
    {
        $this->userManager = $userManager;
        $this->em = $em;
    }
    /**
     * {@inheritDoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $userOauthId= $response->getUsername();
        $owner = $response->getResourceOwner()->getName();
        $existingUser =  $this->userManager->findUserByOauth( $owner, $userOauthId );
        if( null !== $existingUser )
        {
            return $existingUser;
        }


        if (null !== $response->getEmail()) {
            $existingUser = $this->userManager->findUserByEmail($response->getEmail());
            if (null !== $existingUser)
            {
                return $this->updateUserByOAuthUserResponse($existingUser, $response);
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
        $providerName = $response->getResourceOwner()->getName();
        $userOauthId= $response->getUsername();

        $userOauth = new UserOauth();
        $userOauth->setCanonicalId($userOauthId);
        $userOauth->setProvider($providerName);

        $userOauth->setUser($user);


        $this->em->persist( $userOauth );
        $this->em->flush();

        return $user;
    }
}
