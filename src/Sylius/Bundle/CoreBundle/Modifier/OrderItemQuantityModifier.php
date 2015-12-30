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
        $itemQuantity = $orderItem->getQuantity();

        if ($targetQuantity > $itemQuantity) {
            $this->increaseUnitsNumber($orderItem, $targetQuantity - $itemQuantity);
        } else if ($targetQuantity < $itemQuantity) {
            $this->decreaseUnitsNumber($orderItem, $itemQuantity - $targetQuantity);
        }

    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $increaseBy
     */
    private function increaseUnitsNumber(OrderItemInterface $orderItem, $increaseBy)
    {
        for ($i = 0; $i < $increaseBy; $i++) {
            $unit = $this->orderItemUnitFactory->createNew();
            $orderItem->addUnit($unit);
        }
    }

    /**
     * @param OrderItemInterface $orderItem
     * @param int $decreaseBy
     */
    private function decreaseUnitsNumber(OrderItemInterface $orderItem, $decreaseBy)
    {
        foreach ($orderItem->getUnits() as $unit) {
            if (0 >= $decreaseBy--) {
                break;
            }

            $orderItem->removeUnit($unit);
        }
    }
}
