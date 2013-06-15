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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Bundle\AddressingBundle\Model\AddressInterface;
use Sylius\Bundle\CartBundle\Entity\Cart as BaseCart;
use Sylius\Bundle\CoreBundle\Model\CartInterface;
use Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface;

/**
 * Cart entity.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart extends BaseCart implements CartInterface
{
    /**
     * Shipping address.
     *
     * @var AddressInterface
     */
    protected $shippingAddress;

    /**
     * Billing address.
     *
     * @var AddressInterface
     */
    protected $billingAddress;

    /**
     * Shipping method.
     *
     * @var ShippingMethodInterface
     */
    protected $shippingMethod;

    /**
     * Payment method.
     *
     * @var PaymentMethodInterface
     */
    protected $paymentMethod;

    /**
     * {@inheritdoc}
     */
    public function getShippables()
    {
        $shippables = new ArrayCollection();

        foreach ($this->items as $item) {
            $shippables->add($item->getVariant());
        }

        return $shippables;
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
    public function setShippingAddress(AddressInterface $address = null)
    {
        $this->shippingAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $address = null)
    {
        $this->billingAddress = $address;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethod(ShippingMethodInterface $method = null)
    {
        $this->shippingMethod = $method;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function setPaymentMethod(PaymentMethodInterface $method = null)
    {
        $this->paymentMethod = $method;

        return $this;
    }
}
