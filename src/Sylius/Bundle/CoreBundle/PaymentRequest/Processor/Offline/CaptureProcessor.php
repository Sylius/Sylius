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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Offline;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Webmozart\Assert\Assert;

final class CaptureProcessor implements CaptureProcessorInterface
{
    public function process(PaymentRequestInterface $paymentRequest): void
    {
        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        $responseData = $payment->getDetails();
        if (PaymentInterface::STATE_NEW === $payment->getState()) {
            $responseData = [
                'paid' => false,
            ];
        }

        $paymentRequest->setResponseData($responseData);
        $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED);
    }
}
