<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Reactor\BeforePlacedOrder;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Reactor\BeforePlacedOrder\AssignTokenReactor;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;

final class AssignTokenReactorSpec extends ObjectBehavior
{
    function let(OrderTokenAssignerInterface $orderTokenAssigner): void
    {
        $this->beConstructedWith($orderTokenAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignTokenReactor::class);
    }

    function it_assigns_order_tokens(
        OrderInterface $order,
        OrderTokenAssignerInterface $orderTokenAssigner,
    ): void {
        $orderTokenAssigner->assignTokenValue($order)->shouldBeCalled();

        $this->react($order);
    }
}
