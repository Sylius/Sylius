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

use Sylius\Bundle\AddressingBundle\Entity\Address as BaseAddress;
use FOS\UserBundle\Model\UserInterface;

/**
 * Address entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class Address extends BaseAddress
{
    /**
     * User.
     *
     * @var UserInterface
     */
     protected $user;

    /**
     * Get the user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the user.
     *
     * return Address
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }
}
