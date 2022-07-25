<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Controller\Payment;

use Sylius\Bundle\ApiBundle\Provider\CompositePaymentConfigurationProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Webmozart\Assert\Assert;

/** @experimental */
final class GetPaymentConfiguration
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private CompositePaymentConfigurationProviderInterface $compositePaymentConfigurationProvider,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->findOneByOrderToken(
            $request->attributes->get('paymentId'),
            $request->attributes->get('id'),
        );

        Assert::notNull($payment);

        return new JsonResponse($this->compositePaymentConfigurationProvider->provide($payment));
    }
}
