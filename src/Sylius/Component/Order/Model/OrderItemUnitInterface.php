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

namespace Sylius\Component\Order\Model;

use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderItemUnitInterface extends ResourceInterface, AdjustableInterface
{
    /**
     * @return int
     */
    public function getTotal(): int;

    /**
     * @return OrderItemInterface
     */
    public function getOrderItem(): OrderItemInterface;
}
