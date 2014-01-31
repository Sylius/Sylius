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
 * OrderUpdateMailerInterface
 *
 * @author Myke Hines <myke@webhines.com>
 */
interface OrderUpdateMailerInterface
{
    public function sendOrderUpdate(OrderInterface $order, HistoryInterface $history = null);
}
