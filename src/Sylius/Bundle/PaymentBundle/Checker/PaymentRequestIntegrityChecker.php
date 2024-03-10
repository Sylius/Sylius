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

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
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

        return $paymentRequest;
    }
}
