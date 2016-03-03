<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
interface OrderRecalculatorInterface
{
    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function recalculate(OrderInterface $order);
}
