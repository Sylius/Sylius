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

interface OrderInventoryOperatorInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function cancel(OrderInterface $order): void;

    public function hold(OrderInterface $order): void;

    /**
     * @throws \InvalidArgumentException
     */
    public function sell(OrderInterface $order): void;
}
