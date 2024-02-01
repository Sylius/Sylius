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
use Sylius\Bundle\PayumBundle\PaymentRequest\PaymentRequestContextInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class RequestProcessor implements RequestProcessorInterface
{
    public function __construct(
        private Payum $payum,
        private PaymentRequestContextInterface $payumApiContext,
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

        $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED);
    }
}
