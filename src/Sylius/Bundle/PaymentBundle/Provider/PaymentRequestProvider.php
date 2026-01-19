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

namespace Sylius\Bundle\PaymentBundle\Provider;

use Sylius\Bundle\PaymentBundle\Command\PaymentRequestHashAwareInterface;
use Sylius\Component\Payment\Exception\PaymentRequestNotFoundException;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

/** @experimental */
final class PaymentRequestProvider implements PaymentRequestProviderInterface
{
    /** @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository */
    public function __construct(private PaymentRequestRepositoryInterface $paymentRequestRepository)
    {
    }

    public function provide(PaymentRequestHashAwareInterface $command): PaymentRequestInterface
    {
        /** @var PaymentRequestInterface|null $paymentRequest */
        $paymentRequest = $this->paymentRequestRepository->find($command->getHash());
        if (null === $paymentRequest) {
            throw new PaymentRequestNotFoundException();
        }

        return $paymentRequest;
    }
}
