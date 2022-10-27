<?php

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\AfterCompletedCheckoutCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\OrderCheckout\SaveCheckoutCompletionDateCallback;
use Sylius\Component\Core\Model\OrderInterface;

final class SaveCheckoutCompletionDateCallbackSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(SaveCheckoutCompletionDateCallback::class);
    }

    function it_is_called_after_completed_checkout(): void
    {
        $this->shouldImplement(AfterCompletedCheckoutCallbackInterface::class);
    }

    function it_completes_checkout(OrderInterface $order): void
    {
        $order->completeCheckout()->shouldBeCalled();

        $this->call($order);
    }
}
