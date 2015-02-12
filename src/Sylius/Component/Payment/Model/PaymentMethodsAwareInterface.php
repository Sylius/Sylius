<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment\Model;

/**
 * Interface for object referencing multiple payment methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface PaymentMethodsAwareInterface
{
    /**
     * @return Collection|PaymentMethodInterface[]
     */
    public function getPaymentMethods();

    /**
     * @param PaymentMethodInterface $paymentMethod
     *
     * @return Boolean
     */
    public function hasPaymentMethod(PaymentMethodInterface $paymentMethod);

    /**
     * @param PaymentMethodInterface $paymentMethod
     */
    public function addPaymentMethod(PaymentMethodInterface $paymentMethod);

    /**
     * @param PaymentMethodInterface $paymentMethod
     */
    public function removePaymentMethod(PaymentMethodInterface $paymentMethod);
}
