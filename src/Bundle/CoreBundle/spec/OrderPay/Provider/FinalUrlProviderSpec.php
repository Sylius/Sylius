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

namespace spec\Sylius\Bundle\CoreBundle\OrderPay\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\OrderPay\Processor\RouteParametersProcessorInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;

final class FinalUrlProviderSpec extends ObjectBehavior
{
    function let(
        RouteParametersProcessorInterface $routeParametersProcessor,
    ): void {
        $this->beConstructedWith(
            $routeParametersProcessor,
            'final_route',
            [],
            'retry_route',
            [],
        );
    }

    function it_provides_a_final_url_from_null_payment(
        RouteParametersProcessorInterface $routeParametersProcessor,
    ): void {
        $routeParametersProcessor->process('final_route', [], [
            'payment' => null,
            'order' => null,
        ])->willReturn('http://localhost/final_route');

        $this->getUrl(null)->shouldReturn('http://localhost/final_route');
    }

    function it_provides_a_final_url_from_payment_with_state_complete(
        PaymentInterface $payment,
        RouteParametersProcessorInterface $routeParametersProcessor,
    ): void {
        $payment->getOrder()->willReturn(null);
        $payment->getState()->willReturn(BasePaymentInterface::STATE_COMPLETED);

        $routeParametersProcessor->process('final_route', [], [
            'payment' => $payment->getWrappedObject(),
            'order' => null,
        ])->willReturn('http://localhost/final_route');

        $this->getUrl($payment)->shouldReturn('http://localhost/final_route');
    }

    function it_provides_a_final_url_from_payment_with_state_authorized(
        PaymentInterface $payment,
        RouteParametersProcessorInterface $routeParametersProcessor,
    ): void {
        $payment->getOrder()->willReturn(null);
        $payment->getState()->willReturn(BasePaymentInterface::STATE_AUTHORIZED);

        $routeParametersProcessor->process('final_route', [], [
            'payment' => $payment->getWrappedObject(),
            'order' => null,
        ])->willReturn('http://localhost/final_route');

        $this->getUrl($payment)->shouldReturn('http://localhost/final_route');
    }

    function it_provides_a_retry_url_from_payment_with_state_cancelled(
        PaymentInterface $payment,
        RouteParametersProcessorInterface $routeParametersProcessor,
    ): void {
        $payment->getOrder()->willReturn(null);
        $payment->getState()->willReturn(BasePaymentInterface::STATE_CANCELLED)->shouldBeCalledTimes(2);

        $routeParametersProcessor->process('retry_route', [], [
            'payment' => $payment->getWrappedObject(),
            'order' => null,
        ])->willReturn('http://localhost/retry_route');

        $this->getUrl($payment)->shouldReturn('http://localhost/retry_route');
    }
}
