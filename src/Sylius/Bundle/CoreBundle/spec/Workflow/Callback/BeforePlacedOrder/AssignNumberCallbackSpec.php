<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\BeforePlacedOrder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\BeforePlacedOrder\AssignNumberCallback;
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

    function it_assigns_order_numbers(
        OrderInterface $order,
        OrderNumberAssignerInterface $orderNumberAssigner,
    ): void {
        $orderNumberAssigner->assignNumber($order)->shouldBeCalled();

        $this->call($order);
    }
}
