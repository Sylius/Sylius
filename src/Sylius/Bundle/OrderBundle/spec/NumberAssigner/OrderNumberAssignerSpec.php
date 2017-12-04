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

namespace spec\Sylius\Bundle\OrderBundle\NumberAssigner;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Bundle\OrderBundle\NumberGenerator\OrderNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;

final class OrderNumberAssignerSpec extends ObjectBehavior
{
    function let(OrderNumberGeneratorInterface $numberGenerator): void
    {
        $this->beConstructedWith($numberGenerator);
    }

    function it_implements_an_order_number_assigner_interface(): void
    {
        $this->shouldImplement(OrderNumberAssignerInterface::class);
    }

    function it_assigns_a_number_to_an_order(
        OrderInterface $order,
        OrderNumberGeneratorInterface $numberGenerator
    ): void {
        $order->getNumber()->willReturn(null);

        $numberGenerator->generate($order)->willReturn('00000007');
        $order->setNumber('00000007')->shouldBeCalled();

        $this->assignNumber($order);
    }

    function it_does_not_assign_a_number_to_an_order_with_number(
        OrderInterface $order,
        OrderNumberGeneratorInterface $numberGenerator
    ): void {
        $order->getNumber()->willReturn('00000007');

        $numberGenerator->generate($order)->shouldNotBeCalled();
        $order->setNumber(Argument::any())->shouldNotBeCalled();

        $this->assignNumber($order);
    }
}
