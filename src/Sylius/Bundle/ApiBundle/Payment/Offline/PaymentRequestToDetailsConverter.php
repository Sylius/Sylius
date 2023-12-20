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

namespace Sylius\Bundle\ApiBundle\Payment\Offline;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestToDetailsConverter implements PaymentRequestToDetailsConverterInterface
{
    public function convert(PaymentRequestInterface $paymentRequest): array
    {
        $payment = $paymentRequest->getPayment();
        Assert::notNull($payment);

        if (PaymentInterface::STATE_NEW === $payment->getState()) {
            return [
                'paid' => false,
            ];
        }

        return $payment->getDetails();
    }
}
