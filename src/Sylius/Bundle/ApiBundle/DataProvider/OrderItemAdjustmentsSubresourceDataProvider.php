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
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;

final class OrderItemAdjustmentsSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    /** @param OrderItemRepositoryInterface<OrderItemInterface> $orderItemRepository */
    public function __construct(private readonly OrderItemRepositoryInterface $orderItemRepository)
    {
    }

    /** @param array<array-key, mixed> $context */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            is_a($resourceClass, AdjustmentInterface::class, true) &&
            isset($subresourceIdentifiers['tokenValue'], $subresourceIdentifiers['items'])
        ;
    }

    /**
     * @param array<array-key, mixed> $identifiers
     * @param array<array-key, mixed> $context
     *
     * @return iterable<AdjustmentInterface>
     */
    public function getSubresource(string $resourceClass, array $identifiers, array $context, ?string $operationName = null): iterable
    {
        $subresourceIdentifiers = $context['subresource_identifiers'];

        $orderItem = $this->orderItemRepository->findOneByIdAndOrderTokenValue(
            (int) $subresourceIdentifiers['items'],
            $subresourceIdentifiers['tokenValue'],
        );
        if (null === $orderItem) {
            return [];
        }

        return $orderItem->getAdjustmentsRecursively();
    }
}
