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
use Sylius\Component\Customer\Model\Customer as BaseCustomer;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class Customer extends BaseCustomer implements CustomerInterface, ProductReviewerInterface
{
    /**
     * @var ArrayCollection
     */
    protected $orders;

    /**
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * @var ArrayCollection
     */
    protected $addresses;

    /**
     * @var UserInterface
     */
    protected $user;

    public function __construct()
    {
        parent::__construct();

        $this->orders = new ArrayCollection();
        $this->addresses = new ArrayCollection();
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

        if (null !== $billingAddress) {
            $this->addAddress($billingAddress);
        }
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

        if (null !== $shippingAddress) {
            $this->addAddress($shippingAddress);
        }
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
            $address->setCustomer($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAddress(AddressInterface $address)
    {
        $this->addresses->removeElement($address);
        $address->setCustomer(null);
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
    public function hasUser()
    {
        return null !== $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setUser(BaseUserInterface $user = null)
    {
        if ($this->user !== $user) {
            $this->user = $user;
            $this->assignCustomer($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    protected function assignCustomer(UserInterface $user = null)
    {
        if (null !== $user) {
            $user->setCustomer($this);
        }
    }
}
