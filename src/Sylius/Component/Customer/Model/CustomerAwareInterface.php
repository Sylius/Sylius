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

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
interface CustomerAwareInterface
{
    /**
     * @return CustomerInterface
     */
    public function getCustomer();

    /**
     * @param CustomerInterface $customer
     */
    public function setCustomer(CustomerInterface $customer = null);
}
