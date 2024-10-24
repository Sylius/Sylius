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

namespace Sylius\Bundle\PaymentBundle\CommandProvider\Offline;

use Sylius\Bundle\PaymentBundle\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandProvider\PaymentRequestCommandProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

/** @experimental */
final class CapturePaymentRequestCommandProvider implements PaymentRequestCommandProviderInterface
{
    public function supports(PaymentRequestInterface $paymentRequest): bool
    {
        return $paymentRequest->getAction() === PaymentRequestInterface::ACTION_CAPTURE;
    }

    public function provide(PaymentRequestInterface $paymentRequest): object
    {
        return new CapturePaymentRequest($paymentRequest->getHash()?->toBinary());
    }
}
