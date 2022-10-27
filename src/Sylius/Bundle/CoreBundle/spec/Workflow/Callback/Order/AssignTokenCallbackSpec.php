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
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AssignTokenCallback;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\BeforePlacedOrderCallbackInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;

final class AssignTokenCallbackSpec extends ObjectBehavior
{
    function let(OrderTokenAssignerInterface $orderTokenAssigner): void
    {
        $this->beConstructedWith($orderTokenAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignTokenCallback::class);
    }

    function it_is_called_before_placed_order(): void
    {
        $this->shouldImplement(BeforePlacedOrderCallbackInterface::class);
    }

    function it_assigns_order_tokens(
        OrderInterface $order,
        OrderTokenAssignerInterface $orderTokenAssigner,
    ): void {
        $orderTokenAssigner->assignTokenValue($order)->shouldBeCalled();

        $this->call($order);
    }
}
