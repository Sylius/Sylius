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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\User\Model\User as BaseUser;

class ShopUser extends BaseUser implements ShopUserInterface
{
    /** @var BaseCustomerInterface|null */
    protected $customer;

    public function getCustomer(): ?BaseCustomerInterface
    {
        return $this->customer;
    }

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

    public function getEmail(): ?string
    {
        if (null === $this->customer) {
            return null;
        }

        return $this->customer->getEmail();
    }

    public function setEmail(?string $email): void
    {
        if (null === $this->customer) {
            throw new UnexpectedTypeException($this->customer, BaseCustomerInterface::class);
        }

        $this->customer->setEmail($email);
    }

    public function getEmailCanonical(): ?string
    {
        if (null === $this->customer) {
            return null;
        }

        return $this->customer->getEmailCanonical();
    }

    public function setEmailCanonical(?string $emailCanonical): void
    {
        if (null === $this->customer) {
            throw new UnexpectedTypeException($this->customer, BaseCustomerInterface::class);
        }

        $this->customer->setEmailCanonical($emailCanonical);
    }
}
