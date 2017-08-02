<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Inventory\Operator;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface OrderInventoryOperatorInterface
{
    /**
     * @param OrderInterface $order
     *
     * @throws \InvalidArgumentException
     */
    public function cancel(OrderInterface $order);

    /**
     * @param OrderInterface $order
     */
    public function hold(OrderInterface $order);

    /**
     * @param OrderInterface $order
     *
     * @throws \InvalidArgumentException
     */
    public function sell(OrderInterface $order);
}
