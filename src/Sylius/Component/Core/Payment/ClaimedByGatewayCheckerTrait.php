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

namespace Sylius\Component\Core\Payment;

use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

trait ClaimedByGatewayCheckerTrait
{
    private function isClaimedByGateway(PaymentInterface $payment): bool
    {
        $claimingActions = [PaymentRequestInterface::ACTION_CAPTURE, PaymentRequestInterface::ACTION_AUTHORIZE];
        $nonClaimingStates = [
            PaymentRequestInterface::STATE_NEW,
            PaymentRequestInterface::STATE_FAILED,
            PaymentRequestInterface::STATE_CANCELLED,
        ];

        $claimingPaymentRequests = $payment->getPaymentRequests()->filter(
            static function (PaymentRequestInterface $paymentRequest) use ($claimingActions, $nonClaimingStates): bool {
                return
                    in_array($paymentRequest->getAction(), $claimingActions, true) &&
                    !in_array($paymentRequest->getState(), $nonClaimingStates, true);
            },
        );

        return !$claimingPaymentRequests->isEmpty();
    }
}
