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

use Sylius\Component\Addressing\Model\Address as BaseAddress;
use Sylius\Component\Customer\Model\CustomerInterface as BaseCustomerInterface;
use Webmozart\Assert\Assert;

class Address extends BaseAddress implements AddressInterface
{
    /** @var CustomerInterface|null */
    protected $customer;

    public function getCustomer(): ?BaseCustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?BaseCustomerInterface $customer): void
    {
        Assert::nullOrIsInstanceOf($customer, CustomerInterface::class);
        $this->customer = $customer;
    }
}
