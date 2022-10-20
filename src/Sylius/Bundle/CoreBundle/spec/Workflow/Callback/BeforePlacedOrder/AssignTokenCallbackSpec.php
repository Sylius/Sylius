<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\BeforePlacedOrder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\BeforePlacedOrder\AssignTokenCallback;
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

    function it_assigns_order_tokens(
        OrderInterface $order,
        OrderTokenAssignerInterface $orderTokenAssigner,
    ): void {
        $orderTokenAssigner->assignTokenValue($order)->shouldBeCalled();

        $this->run($order);
    }
}
