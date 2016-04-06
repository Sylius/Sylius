<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Order;

use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface OrderPaymentsPageInterface
{
    /**
     * @param PaymentInterface $payment
     */
    public function clickPayButtonForGivenPayment(PaymentInterface $payment);

    /**
     * @param int $timeout
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     */
    public function waitForResponse($timeout, array $parameters);

    /**
     * @param string $state
     *
     * @return int
     */
    public function countPaymentWithSpecificState($state);
}
