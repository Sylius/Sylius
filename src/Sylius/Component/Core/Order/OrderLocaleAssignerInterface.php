<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Order;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface OrderLocaleAssignerInterface
{
    /**
     * @param OrderInterface $order
     */
    public function assignLocale(OrderInterface $order);
}
