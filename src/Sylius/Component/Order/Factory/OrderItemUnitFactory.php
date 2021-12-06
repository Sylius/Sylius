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

namespace Sylius\Component\Order\Factory;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

class OrderItemUnitFactory implements OrderItemUnitFactoryInterface
{
    /**
     * @var string
     *
     * @psalm-var class-string
     */
    private $className;

    /**
     * @psalm-param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @throws UnsupportedMethodException
     */
    public function createNew(): OrderItemUnitInterface
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function createForItem(OrderItemInterface $orderItem): OrderItemUnitInterface
    {
        return new $this->className($orderItem);
    }
}
