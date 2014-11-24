<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Customer\Model\Customer as BaseCustomer;

class Customer extends BaseCustomer implements CustomerInterface
{
    /**
     * @var Collection|OrderInterface[]
     */
    protected $orders;

    public function __construct()
    {
        parent::__construct();

        $this->orders = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
