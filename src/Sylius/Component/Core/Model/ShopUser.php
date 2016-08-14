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
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class ShopUser extends IdentifiableUser implements ShopUserInterface
{
    /**
     * @var CustomerInterface
     */
    protected $customer;

    public function __construct()
    {
        parent::__construct();
        $this->authorizationRoles = new ArrayCollection();
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
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(BaseCustomerInterface $customer = null)
    {
        if ($this->customer !== $customer) {
            $this->customer = $customer;
            $this->assignUser($customer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->customer->getEmail();
    }
    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->customer->setEmail($email);
    }
    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical()
    {
        return $this->customer->getEmailCanonical();
    }
    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->customer->setEmailCanonical($emailCanonical);
    }

    /**
     * @param CustomerInterface $customer
     */
    protected function assignUser(CustomerInterface $customer = null)
    {
        if (null !== $customer) {
            $customer->setUser($this);
        }
    }
}
