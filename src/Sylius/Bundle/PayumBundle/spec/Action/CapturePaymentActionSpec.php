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

namespace spec\Sylius\Bundle\PayumBundle\Action;

use Exception;
use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentInterface as SyliusPaymentInterface;

final class CapturePaymentActionSpec extends ObjectBehavior
{
    function let(PaymentDescriptionProviderInterface $paymentDescriptionProvider): void
    {
        $this->beConstructedWith($paymentDescriptionProvider);
    }

    function it_extends_gateway_aware_action(): void
    {
        $this->shouldHaveType(GatewayAwareAction::class);
    }

    function it_should_throw_exception_when_unsupported_request(Capture $capture): void
    {
        $this->shouldThrow(RequestNotSupportedException::class)->duringExecute($capture);
    }

    function it_should_perform_basic_capture(
        GatewayInterface $gateway,
        Capture $capture,
        PaymentInterface $payment,
        OrderInterface $order
    ): void {
        $this->setGateway($gateway);

        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([]);
        $capture->getModel()->willReturn($payment);

        $capture->setModel(new ArrayObject())->shouldBeCalled();

        $this->execute($capture);
    }

    function it_should_not_overwrite_details(
        GatewayInterface $gateway,
        Capture $capture,
        PaymentInterface $payment,
        OrderInterface $order
    ): void {
        $e = new Exception();
        $previousDetails = ['foo' => 'bar'];
        $failureDetails = ['failure' => 'detail'];
        $this->setGateway($gateway);

        $capture->getModel()->willReturn($payment);
        $payment->getOrder()->willReturn($order);
        $gateway->execute(Argument::any())->willReturn();

        $payment->getDetails()->willReturn($previousDetails);
        $capture->setModel(new ArrayObject($previousDetails))->shouldBeCalled();
        $gateway->execute($capture)->will(
            function ($args) use ($e, $failureDetails): void {
                /** @var Capture $capture */
                [$capture] = $args;
                /** @var SyliusPaymentInterface $payment */
                $payment = $capture->getModel();
                $payment->setDetails($failureDetails);
                throw $e;
            }
        );
        $payment->setDetails($failureDetails)->shouldBeCalledOnce();
        $this->shouldThrow($e)->duringExecute($capture);
    }
}
