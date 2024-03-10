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

namespace Sylius\Bundle\PayumBundle\PaymentRequest\Checker;

use Sylius\Bundle\PaymentBundle\Checker\PaymentRequestIntegrityCheckerInterface;
use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Bundle\PayumBundle\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestIntegrityChecker implements PaymentRequestIntegrityCheckerInterface
{
    public function __construct(
        private PaymentRequestIntegrityCheckerInterface $decoratedPaymentRequestIntegrityChecker,
    ) {
    }

    public function check(PaymentRequestHashAwareInterface $command): PaymentRequestInterface
    {
        $paymentRequest = $this->decoratedPaymentRequestIntegrityChecker->check($command);

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Gateway config cannot be null.');

        return $paymentRequest;
    }
}
