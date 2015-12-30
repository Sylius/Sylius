<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Modifier;

use Sylius\Component\Core\Model\OrderItemInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface OrderItemQuantityModifierInterface
{
    /**
     * @param OrderItemInterface $orderItem
     * @param int $quantity
     */
    public function modify(OrderItemInterface $orderItem, $quantity);
}
