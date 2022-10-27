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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterPlacedOrderCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\CreatePaymentCallback;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class CreatePaymentCallbackSpec extends ObjectBehavior
{
    function let(WorkflowInterface $syliusPaymentWorkflow): void
    {
        $this->beConstructedWith($syliusPaymentWorkflow);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CreatePaymentCallback::class);
    }

    function it_is_called_after_placed_order(): void
    {
        $this->shouldImplement(AfterPlacedOrderCallbackInterface::class);
    }

    function it_creates_payment(
        OrderInterface $order,
        WorkflowInterface $syliusPaymentWorkflow,
        PaymentInterface $firstPayment,
        PaymentInterface $secondPayment,
    ): void {
        $order->getPayments()->willReturn(new ArrayCollection([
            $firstPayment->getWrappedObject(),
            $secondPayment->getWrappedObject(),
        ]));

        $syliusPaymentWorkflow->can($firstPayment, 'create')->willReturn(false);
        $syliusPaymentWorkflow->can($secondPayment, 'create')->willReturn(true);

        $syliusPaymentWorkflow->apply($firstPayment, 'create')->shouldNotBeCalled();
        $syliusPaymentWorkflow->apply($secondPayment, 'create')->shouldBeCalled();

        $this->call($order);
    }
}
