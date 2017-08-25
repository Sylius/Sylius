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

namespace spec\Sylius\Component\Core\Cart\Modifier;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Cart\Modifier\LimitingOrderItemQuantityModifier;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class LimitingOrderItemQuantityModifierSpec extends ObjectBehavior
{
    function let(OrderItemQuantityModifierInterface $itemQuantityModifier)
    {
        $this->beConstructedWith($itemQuantityModifier, 1000);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LimitingOrderItemQuantityModifier::class);
    }

    function it_implements_order_item_modifier_interface()
    {
        $this->shouldImplement(OrderItemQuantityModifierInterface::class);
    }

    function it_restricts_max_item_quantity_to_the_stated_limit(
        OrderItemQuantityModifierInterface $itemQuantityModifier,
        OrderItemInterface $orderItem
    ) {
        $orderItem->getQuantity()->willReturn(0);

        $itemQuantityModifier->modify($orderItem, 1000)->shouldBeCalled();

        $this->modify($orderItem, 9999);
    }
}
