<?php

namespace Ant\Bundle\CoreBundle\Entity;

use Sylius\Bundle\CoreBundle\Entity\User as BaseUser;

/**
 * User entity.
 *
 */
class User extends BaseUser
{
    const ROLE_ADD_PRODUCT = 'ROLE_ADD_PRODUCT';
}