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

use Sylius\Component\Subscription\Model\RecurringSubscription;

/**
 * Subscription implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class Subscription extends RecurringSubscription implements SubscriptionInterface
{
    /**
     * User.
     *
     * @var UserInterface
     */
    protected $user;

    /**
     * Order shipping address.
     *
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * Product variant for subscription.
     *
     * @var ProductVariantInterface
     */
    protected $variant;

    /**
     * Order item for subscription.
     *
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * {@inheritdoc}
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * {@inheritdoc}
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(AddressInterface $address)
    {
        $this->shippingAddress = $address;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariant()
    {
        return $this->variant;
    }

    /**
     * {@inheritdoc}
     */
    public function setVariant(ProductVariantInterface $variant)
    {
        $this->variant = $variant;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getProduct()
    {
        if (!$this->variant) {
            return null;
        }

        return $this->variant->getProduct();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItem(OrderItemInterface $orderItem)
    {
        $this->orderItem = $orderItem;
    }
}
