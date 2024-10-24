<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Action\GatewayAwareAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Authorize;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class AuthorizePaymentActionSpec extends ObjectBehavior
{
    function let(PaymentDescriptionProviderInterface $paymentDescriptionProvider): void
    {
        $this->beConstructedWith($paymentDescriptionProvider);
    }

    function it_extends_gateway_aware_action(): void
    {
        $this->shouldHaveType(GatewayAwareAction::class);
    }

    function it_should_throw_exception_when_unsupported_request(Authorize $authorize): void
    {
        $this->shouldThrow(RequestNotSupportedException::class)->duringExecute($authorize);
    }

    function it_should_perform_basic_authorize(
        GatewayInterface $gateway,
        Authorize $authorize,
        PaymentInterface $payment,
        OrderInterface $order,
    ): void {
        $this->setGateway($gateway);
        $payment->getOrder()->willReturn($order);
        $payment->getDetails()->willReturn([]);
        $authorize->getModel()->willReturn($payment);
        $payment->setDetails([])->shouldBeCalled();
        $authorize->setModel(new ArrayObject())->shouldBeCalled();
        $this->execute($authorize);
    }
}
