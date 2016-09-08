<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Inventory\Handler;

use Sylius\Component\Core\Exception\HandleException;
use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface PaidOrderInventoryHandlerInterface
{
    /**
     * @param OrderInterface $order
     *
     * @throws HandleException
     */
    public function handle(OrderInterface $order);
}
