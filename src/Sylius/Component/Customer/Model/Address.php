<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Customer\Model;

use Sylius\Component\Addressing\Model\Address as BaseAddress;

/**
 * Address entity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class Address extends BaseAddress implements AddressInterface
{
    /**
     * Customer.
     *
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
    public function setCustomer(CustomerInterface $customer = null)
    {
        $this->customer = $customer;
    }
}
