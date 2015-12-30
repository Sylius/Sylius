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
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemQuantityModifier implements OrderItemQuantityModifierInterface
{
    /**
     * @var FactoryInterface
     */
    private $orderItemUnitFactory;

    /**
     * @param FactoryInterface $orderItemUnitFactory
     */
    public function __construct(FactoryInterface $orderItemUnitFactory)
    {
        $this->orderItemUnitFactory = $orderItemUnitFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(OrderItemInterface $orderItem, $targetQuantity)
    {
        if ($targetQuantity === $itemQuantity = $orderItem->getQuantity()) {
            return;
        }

        if ($targetQuantity > $itemQuantity) {
            $this->increaseUnitsNumber($orderItem, $targetQuantity - $itemQuantity);

            return;
        }

        $this->decreaseUnitsNumber($orderItem, $itemQuantity, $targetQuantity);
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $targetUnitsNumber
     */
    private function increaseUnitsNumber(OrderItemInterface $orderItem, $targetUnitsNumber)
    {
        for ($i = 0; $i < $targetUnitsNumber; $i++) {
            $unit = $this->orderItemUnitFactory->createNew();
            $orderItem->addUnit($unit);
        }
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $itemQuantity
     * @param int $targetQuantity
     */
    private function decreaseUnitsNumber(OrderItemInterface $orderItem, $itemQuantity, $targetQuantity)
    {
        for ($i = $itemQuantity-1; $i >= $targetQuantity; $i--) {
            $orderItem->removeUnitByIndex($i);
        }
    }
}
