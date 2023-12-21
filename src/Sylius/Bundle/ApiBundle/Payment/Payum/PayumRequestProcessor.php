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

namespace Sylius\Bundle\ApiBundle\Payment\Payum;

use Payum\Core\Payum;
use Payum\Core\Security\TokenAggregateInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Webmozart\Assert\Assert;

final class PayumRequestProcessor implements PayumRequestProcessorInterface
{
    public function __construct(
        private Payum $payum,
        private PayumApiContextInterface $payumApiContext,
    ) {
    }

    public function process( PaymentRequestInterface $paymentRequest, TokenAggregateInterface $request): void
    {
        $token = $request->getToken();
        Assert::notNull($token);

        $gateway = $this->payum->getGateway($token->getGatewayName());

        $this->payumApiContext->enable($paymentRequest);
        $reply = $gateway->execute($request, true);
        $this->payumApiContext->disable();

        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        if (null === $reply) {
            $details['after_url'] = $token->getAfterUrl();
        }
    }
}
