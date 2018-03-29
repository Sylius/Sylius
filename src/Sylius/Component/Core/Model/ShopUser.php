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

class ShopUser extends BaseUser implements ShopUserInterface
{
    /**
     * @var BaseCustomerInterface|null
     */
    protected $customer;

    /**
     * {@inheritdoc}
     */
    public function getCustomer(): ?BaseCustomerInterface
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomer(?BaseCustomerInterface $customer): void
    {
        if ($this->customer === $customer) {
            return;
        }

        $previousCustomer = $this->customer;
        $this->customer = $customer;

        if ($previousCustomer instanceof CustomerInterface) {
            $previousCustomer->setUser(null);
        }

        if ($customer instanceof CustomerInterface) {
            $customer->setUser($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail(): ?string
    {
        return $this->customer->getEmail();
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail(?string $email): void
    {
        $this->customer->setEmail($email);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailCanonical(): ?string
    {
        return $this->customer->getEmailCanonical();
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical(?string $emailCanonical): void
    {
        $this->customer->setEmailCanonical($emailCanonical);
    }
}
