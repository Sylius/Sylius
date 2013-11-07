<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;

/**
 * OrderConfirmationMailerInterface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface OrderConfirmationMailerInterface
{
    public function sendOrderConfirmation(OrderInterface $order);
}
