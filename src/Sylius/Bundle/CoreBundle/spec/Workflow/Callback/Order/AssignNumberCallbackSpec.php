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
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AssignNumberCallback;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\BeforePlacedOrderCallbackInterface;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AssignNumberCallbackSpec extends ObjectBehavior
{
    function let(OrderNumberAssignerInterface $orderNumberAssigner): void
    {
        $this->beConstructedWith($orderNumberAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignNumberCallback::class);
    }

    function it_is_called_before_placed_order(): void
    {
        $this->shouldImplement(BeforePlacedOrderCallbackInterface::class);
    }

    function it_assigns_order_numbers(
        OrderInterface $order,
        OrderNumberAssignerInterface $orderNumberAssigner,
    ): void {
        $orderNumberAssigner->assignNumber($order)->shouldBeCalled();

        $this->call($order);
    }
}
