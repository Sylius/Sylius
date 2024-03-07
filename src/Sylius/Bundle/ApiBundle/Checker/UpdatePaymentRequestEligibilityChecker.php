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

namespace Sylius\Bundle\ApiBundle\Checker;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class UpdatePaymentRequestEligibilityChecker implements UpdatePaymentRequestEligibilityCheckerInterface
{
    public function isEligible(PaymentRequestInterface $paymentRequest): bool
    {
        return in_array($paymentRequest->getState(), [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING]);
    }
}
