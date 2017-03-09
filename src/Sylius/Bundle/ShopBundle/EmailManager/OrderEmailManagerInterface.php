<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShopBundle\EmailManager;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
interface OrderEmailManagerInterface
{
    /**
     * @param OrderInterface $order
     */
    public function sendConfirmationEmail(OrderInterface $order);
}
