<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\User as BaseUser;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;

/**
 * User model.
 *
 * @author Paweł Jędrzjewski <pjedrzejewski@diweb.pl>
 */
class User extends BaseUser implements UserInterface
{
    protected $amazonId;
    protected $facebookId;
    protected $googleId;
    protected $firstName;
    protected $lastName;
    protected $createdAt;
    protected $updatedAt;
    protected $deletedAt;
    protected $currency;
    protected $orders;
    protected $billingAddress;
    protected $shippingAddress;
    protected $addresses;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->orders    = new ArrayCollection();
        $this->addresses = new ArrayCollection();

        parent::__construct();
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Set ID of Amazon account attached to the user
     *
     * @param string $amazonId
     *
     * @return User
     */
    public function setAmazonId($amazonId)
    {
        $this->amazonId = $amazonId;

        return $this;
    }

    /**
     * Get ID of Amazon account attached to the user
     *
     * @return string|null
     */
    public function getAmazonId()
    {
        return $this->amazonId;
    }

    /**
     * Set ID of Facebook account attached to the user
     *
     * @param string $facebookId
     *
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    /**
     * Get ID of Facebook account attached to the user
     *
     * @return string|null
     */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /**
     * Set ID of Google account attached to the user
     *
     * @param string $googleId
     *
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->googleId = $googleId;

        return $this;
    }

    /**
     * Get ID of Google account attached to the user
     *
     * @return string|null
     */
    public function getGoogleId()
    {
        return $this->googleId;
    }

    /**
     * Get orders
     *
     * @return ArrayCollection
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * Set billingAddress
     *
     * @param  AddressInterface $billingAddress
     * @return User
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
     * Get billingAddress
     *
     * @return AddressInterface
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * Set shippingAddress
     *
     * @param  AddressInterface $shippingAddress
     * @return User
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
     * Get shippingAddress
     *
     * @return AddressInterface
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Add address
     *
     * @param  AddressInterface $address
     * @return User
     */
    public function addAddress(AddressInterface $address)
    {
        if (!$this->hasAddress($address)) {
            $this->addresses[] = $address;
        }

        return $this;
    }

    /**
     * Remove address
     *
     * @param AddressInterface $address
     */
    public function removeAddress(AddressInterface $address)
    {
        $this->addresses->removeElement($address);

        return $this;
    }

    /**
     * Has address
     *
     * @param  AddressInterface $address
     * @return bool
     */
    public function hasAddress(AddressInterface $address)
    {
        return $this->addresses->contains($address);
    }

    /**
     * Get addresses
     *
     * @return AddressInterface[]
     */
    public function getAddresses()
    {
        return $this->addresses;
    }

    public function getFullName()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isDeleted()
    {
        return null !== $this->deletedAt && new \DateTime() >= $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        parent::setEmail($email);
        $this->setUsername($email);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        parent::setEmailCanonical($emailCanonical);
        $this->setUsernameCanonical($emailCanonical);

        return $this;
    }
}
