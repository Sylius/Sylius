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

namespace Sylius\Component\Core\Cart\Modifier;

use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class LimitingOrderItemQuantityModifier implements OrderItemQuantityModifierInterface
{
    /**
     * @var OrderItemQuantityModifierInterface
     */
    private $decoratedOrderItemQuantityModifier;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param OrderItemQuantityModifierInterface $decoratedOrderItemQuantityModifier
     * @param int $limit
     */
    public function __construct(OrderItemQuantityModifierInterface $decoratedOrderItemQuantityModifier, $limit)
    {
        $this->decoratedOrderItemQuantityModifier = $decoratedOrderItemQuantityModifier;
        $this->limit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function modify(OrderItemInterface $orderItem, $targetQuantity)
    {
        $targetQuantity = min($targetQuantity, $this->limit);

        $this->decoratedOrderItemQuantityModifier->modify($orderItem, $targetQuantity);
    }
}
