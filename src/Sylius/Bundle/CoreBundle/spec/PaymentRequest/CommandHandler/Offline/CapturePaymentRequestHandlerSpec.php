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
use Sylius\Bundle\CoreBundle\PaymentRequest\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Offline\CaptureProcessorInterface;
use Sylius\Bundle\CoreBundle\PaymentRequest\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class CapturePaymentRequestHandlerSpec extends ObjectBehavior
{
    function let(
        PaymentRequestProviderInterface $paymentRequestProvider
    ): void {
        $this->beConstructedWith($paymentRequestProvider);
    }

    function it_processes_offline_capture(
        PaymentRequestProviderInterface $paymentRequestProvider,
        PaymentRequestInterface $paymentRequest
    ): void {
        $capturePaymentRequest = new CapturePaymentRequest('hash');
        $paymentRequestProvider->provide($capturePaymentRequest)->willReturn($paymentRequest);
        $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED)->shouldBeCalled();

        $this->__invoke($capturePaymentRequest);
    }
}
