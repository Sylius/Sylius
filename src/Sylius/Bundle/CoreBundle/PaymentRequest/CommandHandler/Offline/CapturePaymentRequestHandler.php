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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\CommandHandler\Offline;

use Sylius\Bundle\CoreBundle\PaymentRequest\Checker\PaymentRequestIntegrityCheckerInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Offline\CaptureProcessorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

/** @experimental */
final class CapturePaymentRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly PaymentRequestIntegrityCheckerInterface $paymentRequestIntegrityChecker,
        private readonly CaptureProcessorInterface $offlineCaptureProcessor
    ) {
    }

    public function __invoke(CapturePaymentRequest $capturePaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestIntegrityChecker->check($capturePaymentRequest);

        $this->offlineCaptureProcessor->process($paymentRequest);
    }
}
