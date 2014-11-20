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

/**
 * SubscriptionInterface
 *
 * Subscription which is linked to a User and Shipping Address
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionInterface extends RecurringSubscriptionInterface, UserAwareInterface
{
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
     * @return ProductVariantInterface
     */
    public function getVariant();

    /**
     * @param ProductVariantInterface $variant
     * @return $this
     */
    public function setVariant(ProductVariantInterface $variant);

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
