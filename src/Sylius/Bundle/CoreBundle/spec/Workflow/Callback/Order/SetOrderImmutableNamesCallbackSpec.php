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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\Order;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterPlacedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\SetOrderImmutableNamesCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Order\OrderItemNamesSetterInterface;

final class SetOrderImmutableNamesCallbackSpec extends ObjectBehavior
{
    function let(OrderItemNamesSetterInterface $orderItemNamesSetter): void
    {
        $this->beConstructedWith($orderItemNamesSetter);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(SetOrderImmutableNamesCallback::class);
    }

    function it_is_called_after_placed_order(): void
    {
        $this->shouldImplement(AfterPlacedOrderCallbackInterface::class);
    }

    function it_sets_order_immutable_names(
        OrderInterface $order,
        OrderItemNamesSetterInterface $orderItemNamesSetter,
    ): void
    {
        $orderItemNamesSetter->__invoke($order)->shouldBeCalled();

        $this->call($order);
    }
}
