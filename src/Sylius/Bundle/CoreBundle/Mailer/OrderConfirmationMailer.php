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
 * OrderConfirmationMailer implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class OrderConfirmationMailer extends AbstractMailer implements OrderConfirmationMailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendOrderConfirmation(OrderInterface $order)
    {
        if (!$user = $order->getUser()) {
            throw new \InvalidArgumentException('Order has to belong to a User');
        }

        $this->sendEmail(array('order' => $order), $user->getEmail());
    }
}
