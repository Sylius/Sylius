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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Processor;

use Payum\Core\Payum;
use Payum\Core\Security\TokenAggregateInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PayumBundle\PaymentRequest\Context\PaymentRequestContextInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;

/** @experimental */
final readonly class RequestProcessor implements RequestProcessorInterface
{
    public function __construct(
        private Payum $payum,
        private PaymentRequestContextInterface $payumApiContext,
        private StateMachineInterface $stateMachine,
    ) {
    }

    public function process(
        PaymentRequestInterface $paymentRequest,
        mixed $request,
        string $gatewayName,
    ): void {
        $gateway = $this->payum->getGateway($gatewayName);

        $this->payumApiContext->enable($paymentRequest);
        $reply = $gateway->execute($request, true);
        $this->payumApiContext->disable();

        if (null !== $reply) {
            return;
        }

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE,
        );

        if (false === $request instanceof TokenAggregateInterface) {
            return;
        }

        $token = $request->getToken();
        if (null === $token) {
            return;
        }

        $this->payum->getHttpRequestVerifier()->invalidate($token);
    }
}
