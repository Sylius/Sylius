<?php

namespace spec\Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PayumBundle\Action\CapturePaymentAction;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

/**
 * @author Stefan Doorn <stefan@efectos.nl>
 */
final class CapturePaymentActionSpec extends ObjectBehavior
{
    function let(PaymentDescriptionProviderInterface $paymentDescriptionProvider)
    {
        $this->beConstructedWith($paymentDescriptionProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CapturePaymentAction::class);
    }

    function it_extends_gateway_aware_action()
    {
        $this->shouldHaveType(GatewayAwareAction::class);
    }

    function it_should_throw_exception_when_unsupported_request(Capture $capture)
    {
        $this->shouldThrow('\Payum\Core\Exception\RequestNotSupportedException')->duringExecute($capture);
    }

    function it_should_perform_basic_capture(
        PaymentDescriptionProviderInterface $paymentDescriptionProvider,
        GatewayInterface $gateway,
        Capture $capture,
        PaymentInterface $payment,
        OrderInterface $order
    ) {
        $this->setGateway($gateway);

        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([]);
        $capture->getModel()->willReturn($payment);

        $payment->setDetails([])->shouldBeCalled();
        $capture->setModel(new ArrayObject())->shouldBeCalled();

        $this->execute($capture);
    }
}
