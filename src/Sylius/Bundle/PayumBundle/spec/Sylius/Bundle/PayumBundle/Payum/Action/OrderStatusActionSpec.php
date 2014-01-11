<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\PayumBundle\Payum\Action;

use PhpSpec\ObjectBehavior;

class OrderStatusActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Action\OrderStatusAction');
    }

    function it_extends_payum_payment_aware_action()
    {
        $this->shouldHaveType('Payum\Core\Action\PaymentAwareAction');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     * @param Payum\Core\Request\StatusRequestInterface     $statusRequest
     */
    function it_should_support_status_request_with_order_model($order, $statusRequest)
    {
        $statusRequest->getModel()->willReturn($order);

        $this->supports($statusRequest)->shouldReturn(true);
    }

    /**
     * @param Payum\Core\Request\StatusRequestInterface $statusRequest
     */
    function it_should_not_support_status_request_with_no_order_model($statusRequest)
    {
        $statusRequest->getModel()->willReturn('foo');

        $this->supports($statusRequest)->shouldReturn(false);
    }

    function it_should_not_support_any_no_status_requests()
    {
        $this->supports('foo')->shouldReturn(false);
    }

    function it_throws_exception_if_executing_not_supported_request()
    {
        $notSupportedRequest = 'foo';

        $this
            ->shouldThrow('Payum\Core\Exception\RequestNotSupportedException')
            ->duringExecute($notSupportedRequest)
        ;
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface       $order
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentInterface $payment
     * @param Payum\Core\Request\StatusRequestInterface           $statusRequest
     */
    function it_should_mark_new_if_order_have_empty_payment_details($order, $payment, $statusRequest)
    {
        $statusRequest->getModel()->willReturn($order);
        $statusRequest->markNew()->shouldBeCalled();

        $order->getPayment()->willReturn($payment);
        $payment->getDetails()->willReturn(array());

        $this->execute($statusRequest);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface       $order
     * @param Sylius\Bundle\PaymentsBundle\Model\PaymentInterface $payment
     * @param Payum\Core\Request\StatusRequestInterface           $statusRequest
     * @param Payum\Core\PaymentInterface                         $payment
     */
    function it_should_do_status_subrequest_with_payment_details_as_model($order, $payment, $statusRequest, $payment)
    {
        $details = array('foo' => 'foo', 'bar' => 'baz');

        $statusRequest->getModel()->willReturn($order);
        $statusRequest->setModel($details)->shouldBeCalled();
        $statusRequest->setModel($order)->shouldBeCalled();

        $payment->execute($statusRequest)->shouldBeCalled();

        $order->getPayment()->willReturn($payment);
        $payment->getDetails()->willReturn($details);

        $this->setPayment($payment);
        $this->execute($statusRequest);
    }
}
