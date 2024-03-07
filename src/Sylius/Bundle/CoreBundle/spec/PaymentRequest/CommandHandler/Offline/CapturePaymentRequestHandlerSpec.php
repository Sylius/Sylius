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

namespace spec\Sylius\Bundle\CoreBundle\PaymentRequest\CommandHandler\Offline;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\PaymentRequest\Checker\PaymentRequestIntegrityCheckerInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Offline\CaptureProcessorInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class CapturePaymentRequestHandlerSpec extends ObjectBehavior
{
    function let(
        PaymentRequestIntegrityCheckerInterface $paymentRequestIntegrityChecker,
        CaptureProcessorInterface $offlineCaptureProcessor
    ): void {
        $this->beConstructedWith($paymentRequestIntegrityChecker, $offlineCaptureProcessor);
    }

    function it_processes_offline_capture(
        PaymentRequestIntegrityCheckerInterface $paymentRequestIntegrityChecker,
        CaptureProcessorInterface $offlineCaptureProcessor,
        PaymentRequestInterface $paymentRequest
    ): void {
        $capturePaymentRequest = new CapturePaymentRequest('hash');
        $paymentRequestIntegrityChecker->check($capturePaymentRequest)->willReturn($paymentRequest);

        $this->__invoke($capturePaymentRequest);

        $offlineCaptureProcessor->process($paymentRequest)->shouldHaveBeenCalled();
    }
}
