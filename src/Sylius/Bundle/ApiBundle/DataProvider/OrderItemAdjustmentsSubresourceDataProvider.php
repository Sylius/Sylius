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
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class OrderItemAdjustmentsSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    public function __construct(private OrderItemRepositoryInterface $orderItemRepository)
    {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            is_a($resourceClass, AdjustmentInterface::class, true) &&
            isset($subresourceIdentifiers['tokenValue'], $subresourceIdentifiers['items'])
        ;
    }

    public function getSubresource(string $resourceClass, array $identifiers, array $context, string $operationName = null)
    {
        $subresourceIdentifiers = $context['subresource_identifiers'];

        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->find($subresourceIdentifiers['items']);
        Assert::notNull($orderItem);

        return $orderItem->getAdjustmentsRecursively();
    }
}
