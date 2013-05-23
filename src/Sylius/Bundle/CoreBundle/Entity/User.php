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
    protected $addresses;

    public function __construct()
    {
        $this->orders    = new ArrayCollection();
        $this->addresses = new ArrayCollection();

        parent::__construct();
    }

    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set billingAddress
     *
     * @param \Sylius\Bundle\AddressingBundle\Model\AddressInterface $billingAddress
     * @return User
     */
    public function setBillingAddress(\Sylius\Bundle\AddressingBundle\Model\AddressInterface $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        return $this;
    }

    /**
     * Get billingAddress
     *
     * @return \Sylius\Bundle\AddressingBundle\Model\AddressInterface
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set shippingAddress
     *
     * @param \Sylius\Bundle\AddressingBundle\Model\AddressInterface $shippingAddress
     * @return User
     */
    public function setShippingAddress(\Sylius\Bundle\AddressingBundle\Model\AddressInterface $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return \Sylius\Bundle\AddressingBundle\Model\AddressInterface
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Add addresses
     *
     * @param \Sylius\Bundle\AddressingBundle\Model\AddressInterface $addresses
     * @return User
     */
    public function addAddresse(\Sylius\Bundle\AddressingBundle\Model\AddressInterface $addresse)
    {
        $this->addresses[] = $addresse;

        return $this;
    }

    /**
     * Remove addresses
     *
     * @param \Sylius\Bundle\AddressingBundle\Model\AddressInterface $addresses
     */
    public function removeAddresse(\Sylius\Bundle\AddressingBundle\Model\AddressInterface $addresse)
    {
        $this->addresses->removeElement($addresse);
    }

    /**
     * Get addresses
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAddresses()
    {
        return $this->addresses;
    }
}
