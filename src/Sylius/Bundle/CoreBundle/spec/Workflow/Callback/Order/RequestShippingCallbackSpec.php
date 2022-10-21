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
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\RequestShippingCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\WorkflowInterface;

final class RequestShippingCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusOrderShippingWorkflow): void
    {
        $this->beConstructedWith($syliusOrderShippingWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(RequestShippingCallback::class);
    }

    function it_requests_shipping(
        OrderInterface $order,
        WorkflowInterface $syliusOrderShippingWorkflow,
    ): void {
        $syliusOrderShippingWorkflow->apply($order, 'request_shipping')->shouldBeCalled();

        $this->call($order);
    }
}
