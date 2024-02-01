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

use Payum\Core\Security\TokenInterface;
use Sylius\Bundle\PaymentBundle\CommandDispatcher\PaymentRequestCommandDispatcherInterface;
use Sylius\Component\Payment\Factory\PaymentRequestFactoryInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class AfterTokenizedRequestProcessor implements AfterTokenizedRequestProcessorInterface
{
    public function __construct(
        private PaymentRequestFactoryInterface $paymentRequestFactory,
        private PaymentRequestCommandDispatcherInterface $paymentRequestCommandDispatcher,
    ) {
    }

    public function process(
        PaymentRequestInterface $paymentRequest,
        TokenInterface $token,
    ): void {
        if (PaymentRequestInterface::STATE_COMPLETED !== $paymentRequest->getState()) {
            return;
        }

        $details = $paymentRequest->getResponseData();
        $details['after_url'] = $token->getAfterUrl();
        $paymentRequest->setResponseData($details);

        $newPaymentRequest = $this->paymentRequestFactory->createFromPaymentRequest($paymentRequest);
        $newPaymentRequest->setType(PaymentRequestInterface::DATA_TYPE_STATUS);

        $this->paymentRequestCommandDispatcher->add($newPaymentRequest);
    }
}
