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
    protected $orders;
    protected $billingAddress;
    protected $shippingAddress;

    public function __construct()
    {
        $this->orders = new ArrayCollection();

        parent::__construct();
    }

    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set billingAddress
     *
     * @param \Sylius\Bundle\CoreBundle\Entity\Address $billingAddress
     * @return User
     */
    public function setBillingAddress(\Sylius\Bundle\CoreBundle\Entity\Address $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \Sylius\Bundle\CoreBundle\Entity\Address
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set shippingAddress
     *
     * @param \Sylius\Bundle\CoreBundle\Entity\Address $shippingAddress
     * @return User
     */
    public function setShippingAddress(\Sylius\Bundle\CoreBundle\Entity\Address $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \Sylius\Bundle\CoreBundle\Entity\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }
}
