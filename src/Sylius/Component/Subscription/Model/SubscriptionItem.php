<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Model;

/**
 * SubscriptionItem
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionItem implements SubscriptionItemInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var SubscriptionInterface
     */
    protected $subscription;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * Get identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubscription(SubscriptionInterface $subscription = null)
    {
        $this->subscription = $subscription;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }
}
