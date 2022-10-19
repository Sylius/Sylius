<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Reactor\BeforePlacedOrder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Reactor\BeforePlacedOrder\AssignNumberReactor;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AssignNumberReactorSpec extends ObjectBehavior
{
    function let(OrderNumberAssignerInterface $orderNumberAssigner): void
    {
        $this->beConstructedWith($orderNumberAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignNumberReactor::class);
    }

    function it_assigns_order_numbers(
        OrderInterface $order,
        OrderNumberAssignerInterface $orderNumberAssigner,
    ): void {
        $orderNumberAssigner->assignNumber($order)->shouldBeCalled();

        $this->react($order);
    }
}
