<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Updater;

use Sylius\Component\Core\Model\OrderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
interface OrderUpdaterInterface
{
    /**
     * @param OrderInterface $order
     */
    public function update(OrderInterface $order);
}
