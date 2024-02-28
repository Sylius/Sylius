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

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Checker;

use Sylius\Bundle\CoreBundle\PaymentRequest\Command\PaymentRequestHashAwareInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Webmozart\Assert\Assert;

final class PaymentRequestIntegrityChecker implements PaymentRequestIntegrityCheckerInterface
{
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function check(PaymentRequestHashAwareInterface $command): PaymentRequestInterface
    {
        $hash = $command->getHash();
        Assert::notNull($hash, 'Payment request hash cannot be null.');

        $paymentRequest = $this->paymentRequestRepository->findOneByHash($hash);
        Assert::notNull($paymentRequest, sprintf('Payment request (hash "%s") not found.', $hash));

        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $paymentRequest->getMethod();

        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, 'Gateway config cannot be null.');

        return $paymentRequest;
    }
}
