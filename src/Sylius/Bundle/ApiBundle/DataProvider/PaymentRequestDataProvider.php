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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

/** @experimental */
final class PaymentRequestDataProvider implements RestrictedDataProviderInterface, ItemDataProviderInterface
{
    public function __construct(private readonly PaymentRequestRepositoryInterface $paymentRequestRepository)
    {
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = [])
    {
        $paymentRequest = $this->paymentRequestRepository->find($id);

        if (PaymentRequestInterface::STATE_PROCESSING === $paymentRequest->getState()) {
            return $paymentRequest;
        }

        return null;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, PaymentRequestInterface::class, true) &&
            Request::METHOD_PUT === $context[ContextKeys::HTTP_REQUEST_METHOD_TYPE];
    }
}
