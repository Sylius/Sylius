<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Processor\Order\BeforeCreate;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Processor\Order\BeforeCreate\AssignNumberBeforeOrderCreateProcessor;
use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class AssignNumberProcessorSpec extends ObjectBehavior
{
    function let(OrderNumberAssignerInterface $orderNumberAssigner): void
    {
        $this->beConstructedWith($orderNumberAssigner);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AssignNumberBeforeOrderCreateProcessor::class);
    }

    function it_assigns_order_numbers(
        OrderInterface $order,
        OrderNumberAssignerInterface $orderNumberAssigner,
    ): void {
        $orderNumberAssigner->assignNumber($order)->shouldBeCalled();

        $this->process($order);
    }
}
