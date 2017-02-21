<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Convert;
use Payum\Offline\Constants;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Action\Offline\ConvertPaymentAction;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    function it_is_payum_action()
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_converts_payment_to_offline_action(Convert $request, PaymentInterface $payment)
    {
        $request->getTo()->willReturn('array');
        $request->getSource()->willReturn($payment);

        $request->setResult([
            Constants::FIELD_PAID => false,
        ])->shouldBeCalled();

        $this->execute($request);
    }

    function it_supports_only_convert_request(
        Convert $convertRequest,
        Capture $captureRequest,
        PaymentInterface $payment
    ) {
        $convertRequest->getTo()->willReturn('array');
        $convertRequest->getSource()->willReturn($payment);

        $this->supports($convertRequest)->shouldReturn(true);
        $this->supports($captureRequest)->shouldReturn(false);
    }

    function it_supports_only_converting_to_array_from_payment(
        Convert $fromSomethingElseToSomethingElseRequest,
        Convert $fromPaymentToArrayRequest,
        PaymentInterface $payment,
        PaymentMethodInterface $method
    ) {
        $fromPaymentToArrayRequest->getTo()->willReturn('array');
        $fromPaymentToArrayRequest->getSource()->willReturn($payment);

        $fromSomethingElseToSomethingElseRequest->getTo()->willReturn('json');
        $fromSomethingElseToSomethingElseRequest->getSource()->willReturn($method);

        $this->supports($fromPaymentToArrayRequest)->shouldReturn(true);
        $this->supports($fromSomethingElseToSomethingElseRequest)->shouldReturn(false);
    }
}
