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

use Sylius\Component\Core\Model\OrderInterface;

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
        if (!$email = $order->getEmail()) {
            throw new \InvalidArgumentException('Order must contain customer email');
        }

        $this->sendEmail(array('order' => $order), $email);
    }
}
