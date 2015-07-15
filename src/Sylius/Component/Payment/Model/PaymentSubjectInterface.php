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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PaymentSubjectInterface
{
    /**
     * Get payment method associated with this payment.
     *
     * @return PaymentMethodInterface
     */
    public function getMethod();

    /**
     * Get amount.
     *
     * @return integer
     */
    public function getAmount();

    /**
     * Get payment state
     *
     * @return string
     */
    public function getState();
}
