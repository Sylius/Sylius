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

use Payum\Core\PaymentInterface as PayumPaymentInterface;
use Payum\Core\Request\GetStatusInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentInterface;

class PaymentStatusActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Action\PaymentStatusAction');
    }

    function it_extends_payum_payment_aware_action()
    {
        $this->shouldHaveType('Payum\Core\Action\PaymentAwareAction');
    }

    function it_should_support_status_request_with_order_model(
        PaymentInterface $payment,
        GetStatusInterface $statusRequest
    ) {
        $statusRequest->getModel()->willReturn($payment);

        $this->supports($statusRequest)->shouldReturn(true);
    }

    function it_should_not_support_status_request_with_no_order_model(
        GetStatusInterface $statusRequest
    )  {
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

    function it_should_mark_new_if_order_have_empty_payment_details(
        PaymentInterface $payment,
        GetStatusInterface $statusRequest
    ) {
        $payment->getDetails()->willReturn(array());

        $statusRequest->getModel()->willReturn($payment);
        $statusRequest->markNew()->shouldBeCalled();

        $this->execute($statusRequest);
    }

    function it_should_do_status_subrequest_with_payment_details_as_model(
        PaymentInterface $payment,
        GetStatusInterface $statusRequest,
        PayumPaymentInterface $payment
    ) {
        $details = array('foo' => 'foo', 'bar' => 'baz');

        $statusRequest->getModel()->willReturn($payment);
        $statusRequest->setModel($details)->shouldBeCalled();
        $statusRequest->setModel($payment)->shouldBeCalled();

        $payment->execute($statusRequest)->shouldBeCalled();

        $payment->getDetails()->willReturn($details);

        $this->setPayment($payment);
        $this->execute($statusRequest);
    }
}
