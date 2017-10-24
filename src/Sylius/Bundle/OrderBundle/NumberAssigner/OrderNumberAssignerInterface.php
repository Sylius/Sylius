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

namespace Sylius\Bundle\OrderBundle\NumberAssigner;

use Sylius\Component\Order\Model\OrderInterface;

interface OrderNumberAssignerInterface
{
    /**
     * @param OrderInterface $order
     */
    public function assignNumber(OrderInterface $order): void;
}
