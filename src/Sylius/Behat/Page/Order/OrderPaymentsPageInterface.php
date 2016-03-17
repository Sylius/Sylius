<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Order;

use Sylius\Behat\Page\ElementNotFoundException;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface OrderPaymentsPageInterface
{
    /**
     * @param PaymentInterface $payment
     *
     * @throws ElementNotFoundException
     */
    public function clickPayButtonForGivenPayment(PaymentInterface $payment);

    /**
     * @param int $timeout
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function waitForResponse($timeout, array $parameters);

    /**
     * @param string $state
     *
     * @throws ElementNotFoundException
     *
     * @return int
     */
    public function countPaymentWithSpecificState($state);
}
