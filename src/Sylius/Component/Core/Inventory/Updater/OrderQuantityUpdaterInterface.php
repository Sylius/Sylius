<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Inventory\Updater;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
interface OrderQuantityUpdaterInterface
{
    /**
     * @param OrderInterface $order
     */
    public function increase(OrderInterface $order);

    /**
     * @var OrderInterface $order
     */
    public function decrease(OrderInterface $order);
}
