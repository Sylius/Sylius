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
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\StateGuard\OrderGuardInterface;

class CompleteStepGuardSpec extends ObjectBehavior
{
    function it_implements_order_guard_interface()
    {
        $this->shouldImplement(OrderGuardInterface::class);
    }

    function it_is_satisfied_by_order(OrderInterface $order, AddressInterface $address)
    {
        $order->isEmpty()->willReturn(false);

        $order->getBillingAddress()->willReturn($address);

        $order->isShippingRequired()->willReturn(true);
        $order->getShippingAddress()->willReturn($address);

        $order->getTotal()->willReturn(10);
        $order->hasPayments()->willReturn(true);

        $this->isSatisfiedBy($order)->shouldBe(true);
    }
}
