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

use Sylius\Component\Subscription\Model\RecurringSubscriptionInterface;
use Sylius\Component\Product\Model\VariantInterface;

/**
 * SubscriptionInterface
 *
 * Subscription which is linked to a User and Shipping Address
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionInterface extends RecurringSubscriptionInterface
{
    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user);

    /**
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * @param AddressInterface $address
     * @return $this
     */
    public function setShippingAddress(AddressInterface $address);

    /**
     * @return VariantInterface
     */
    public function getVariant();

    /**
     * @param VariantInterface $variant
     * @return $this
     */
    public function setVariant(VariantInterface $variant);

    /**
     * @return null|ProductInterface
     */
    public function getProduct();

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem();

    /**
     * @param OrderItemInterface $orderItem
     * @return $this
     */
    public function setOrderItem(OrderItemInterface $orderItem);
}
