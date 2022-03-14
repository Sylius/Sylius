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

namespace spec\Sylius\Component\Core\StateGuard;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderItem;
use Sylius\Component\Core\StateGuard\OrderGuardInterface;

class SelectPaymentStepGuardSpec extends ObjectBehavior
{
    function it_implements_order_guard_interface()
    {
        $this->shouldImplement(OrderGuardInterface::class);
    }

    function it_is_satisfied_by_order()
    {
        $order = new Order();
        $order->addItem(new OrderItem());

        $this->isSatisfiedBy($order)->shouldBe(true);
    }
}
