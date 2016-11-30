<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemUnitFactory implements OrderItemUnitFactoryInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnsupportedMethodException
     */
    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    /**
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemUnit
     */
    public function createForItem(OrderItemInterface $orderItem)
    {
        return new $this->className($orderItem);
    }
}
