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

use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Model\HistoryInterface;

/**
 * OrderUpdateMailer implementation
 *
 * @author Myke Hines <myke@webhines.com>
 */
class OrderUpdateMailer extends AbstractMailer implements OrderUpdateMailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendOrderUpdate(OrderInterface $order, HistoryInterface $history = null)
    {
        if (!$user = $order->getUser()) {
            throw new \InvalidArgumentException('Order has to belong to a User');
        }

        $this->sendEmail(array('order' => $order, 'history' => $history), $user->getEmail());
    }
}
