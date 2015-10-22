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

use Sylius\Component\User\Model\CustomerInterface as BaseCustomerInterface;
use Sylius\Component\Affiliate\Model\Affiliate as BaseAffiliate;

/**
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class Affiliate extends BaseAffiliate implements AffiliateInterface
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
        $this->customer = $customer;
    }
}
