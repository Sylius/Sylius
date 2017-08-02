<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Model;

use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Component\User\Model\User as BaseUser;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class ShopUser extends BaseUser implements ShopUserInterface
{
    /**
     * @var CustomerInterface
     */
    protected $customer;

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
