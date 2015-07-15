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
use Payum\Core\Request\Generic;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Payment\Model\PaymentInterface;

class ExecuteSameRequestWithPaymentDetailsActionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PayumBundle\Payum\Action\ExecuteSameRequestWithPaymentDetailsAction');
    }

    public function it_extends_payum_payment_aware_action()
    {
        $this->shouldHaveType('Payum\Core\Action\PaymentAwareAction');
    }

    public function it_should_support_request(Generic $request, PaymentInterface $payment)
    {
        $request->getModel()->willReturn($payment);
        $payment->getDetails()->willReturn(array('foo' => 'foo'));

        $this->supports($request)->shouldReturn(true);
    }

    public function it_should_not_support_generic_request_with_not_payment_model(Generic $request)
    {
        $request->getModel()->willReturn(new \stdClass());

        $this->supports($request)->shouldReturn(false);
    }

    public function it_should_not_support_anything_not_model_request()
    {
        $this->supports(new \stdClass())->shouldReturn(false);
    }

    public function it_throws_exception_if_executing_not_supported_request()
    {
        $this
            ->shouldThrow('Payum\Core\Exception\RequestNotSupportedException')
            ->duringExecute($notSupportedRequest = 'foo')
        ;
    }

    public function it_should_execute_same_request_with_details_wrapped_by_array_object(
        Generic $request,
        PaymentInterface $payment,
        PayumPaymentInterface $payumPayment
    ) {
        $this->setPayment($payumPayment);

        $request->getModel()->willReturn($payment);
        $payment->getDetails()->willReturn(array('foo' => 'fooValue'));

        $request
            ->setModel(Argument::type('Payum\Core\Bridge\Spl\ArrayObject'))
            ->shouldBeCalled()
            ->will(function ($args) use ($request) {
                $request->getModel()->willReturn($args[0]);
            })
        ;
        $request->getModel()->willReturn($payment);

        $payumPayment
            ->execute($request)
            ->shouldBeCalled()
            ->will(function ($args) {
                $details = $args[0]->getModel();
                $details['bar'] = 'barValue';
            })
        ;

        $payment->setDetails(array('foo' => 'fooValue', 'bar' => 'barValue'))->shouldBeCalled();

        $this->execute($request);
    }
}
