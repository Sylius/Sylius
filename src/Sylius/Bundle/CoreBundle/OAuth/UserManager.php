<?php

namespace Sylius\Bundle\CoreBundle\OAuth;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;
use Sylius\Component\Core\Model\UserInterface;

class UserManager extends BaseUserManager
{
    /**
     * Finds a user by oauth id
     *
     * @param string $oauthOwner
     * @param string $oauthId
     * @return UserInterface
     */
    function findUserByOauth( $oauthOwner, $oauthId )
    {
        return $this->repository->findOneByOauth($oauthOwner,$oauthId);
    }
}