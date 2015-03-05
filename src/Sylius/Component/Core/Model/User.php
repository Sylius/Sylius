<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\User\Model\User as BaseUser;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * User model.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class User extends BaseUser implements UserInterface
{
    protected $currency;
    protected $orders;
    protected $authorizationRoles;
    protected $billingAddress;
    protected $shippingAddress;
    protected $addresses;

    public function __construct()
    {
        parent::__construct();
        $this->orders        = new ArrayCollection();
        $this->addresses     = new ArrayCollection();
        $this->authorizationRoles = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $billingAddress = null)
    {
        $this->billingAddress = $billingAddress;

        if (null !== $billingAddress && !$this->hasAddress($billingAddress)) {
            $this->addAddress($billingAddress);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(AddressInterface $shippingAddress = null)
    {
        $this->shippingAddress = $shippingAddress;

        if (null !== $shippingAddress && !$this->hasAddress($shippingAddress)) {
            $this->addAddress($shippingAddress);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function addAddress(AddressInterface $address)
    {
        if (!$this->hasAddress($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAddress(AddressInterface $address)
    {
        $this->addresses->removeElement($address);
        $address->setUser(null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasAddress(AddressInterface $address)
    {
        return $this->addresses->contains($address);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationRoles()
    {
        return $this->authorizationRoles;
    }

    /**
     * {@inheritdoc}
     */
    public function addAuthorizationRole(RoleInterface $role)
    {
        if (!$this->hasAuthorizationRole($role)) {
            $this->authorizationRoles->add($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAuthorizationRole(RoleInterface $role)
    {
        if ($this->hasAuthorizationRole($role)) {
            $this->authorizationRoles->removeElement($role);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAuthorizationRole(RoleInterface $role)
    {
        return $this->authorizationRoles->contains($role);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = parent::getRoles();

        foreach ($this->getAuthorizationRoles() as $role) {
            $roles = array_merge($roles, $role->getSecurityRoles());
        }

        return $roles;
    }
}
