<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * User entity.
 *
 * @author Paweł Jędrzjewski <pjedrzejewski@diweb.pl>
 */
class User extends BaseUser
{
    protected $firstName;

    protected $lastName;

    protected $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();

        parent::__construct();
    }

    public function getOrders()
    {
        return $this->orders;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
}
