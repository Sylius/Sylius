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

namespace Sylius\Bundle\ApiBundle\Controller\Payment;

use Sylius\Bundle\ApiBundle\Exception\PaymentNotFoundException;
use Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class GetPaymentConfiguration
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CompositePaymentConfigurationProviderInterface $compositePaymentConfigurationProvider,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $paymentId = $request->attributes->get('paymentId');
        $tokenValue = $request->attributes->get('tokenValue');
        if (null === $paymentId || null === $tokenValue) {
            throw new PaymentNotFoundException();
        }

        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->findOneByOrderToken($paymentId, $tokenValue);

        if ($payment === null) {
            throw new PaymentNotFoundException();
        }

        return new JsonResponse($this->compositePaymentConfigurationProvider->provide($payment));
    }
}
