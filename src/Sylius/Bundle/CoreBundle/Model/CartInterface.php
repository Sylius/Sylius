<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\CartBundle\Model\CartInterface as BaseCartInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippablesAwareInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Sylius core Order model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartInterface extends BaseCartInterface, ShippablesAwareInterface
{
    /**
     * Get shipping address.
     *
     * @return AddressInterface
     */
    public function getShippingAddress();

    /**
     * Set shipping address.
     *
     * @param AddressInterface $address
     */
    public function setShippingAddress(AddressInterface $address = null);

    /**
     * Get billing address.
     *
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * Set billing address.
     *
     * @param AddressInterface $address
     */
    public function setBillingAddress(AddressInterface $address = null);

    /**
     * Get shipping method selected during checkout.
     *
     * @return null|ShippingMethodInterface
     */
    public function getShippingMethod();

    /**
     * Set shipping method.
     *
     * @param null|ShippingMethodInterface $method
     */
    public function setShippingMethod(ShippingMethodInterface $method = null);

    /**
     * Get payment method selected during checkout.
     *
     * @return null|PaymentMethodInterface
     */
    public function getPaymentMethod();

    /**
     * Set payment method.
     *
     * @param null|PaymentMethodInterface $method
     */
    public function setPaymentMethod(PaymentMethodInterface $method = null);
}
