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

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class OrderAdjustmentsSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            is_a($resourceClass, AdjustmentInterface::class, true) &&
            isset($subresourceIdentifiers['tokenValue']) &&
            !isset($subresourceIdentifiers['items'])
        ;
    }

    public function getSubresource(string $resourceClass, array $identifiers, array $context, ?string $operationName = null)
    {
        $subresourceIdentifiers = $context['subresource_identifiers'];

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneBy(['tokenValue' => $subresourceIdentifiers['tokenValue']]);
        if ($order === null) {
            throw new NotFoundHttpException('Order not found');
        }

        return $order->getAdjustmentsRecursively();
    }
}
