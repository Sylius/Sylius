<?php

namespace Sylius\Bundle\OrderBundle\Factory;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface OrderItemUnitFactoryInterface extends FactoryInterface
{
    /**
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemUnit
     */
    public function createForItem(OrderItemInterface $orderItem);
}
