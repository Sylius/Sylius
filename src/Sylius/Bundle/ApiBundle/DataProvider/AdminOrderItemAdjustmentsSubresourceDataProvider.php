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
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Order\Repository\OrderItemRepositoryInterface;

final class AdminOrderItemAdjustmentsSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    /** @param OrderItemRepositoryInterface<OrderItemInterface> $orderItemRepository */
    public function __construct(
        private readonly OrderItemRepositoryInterface $orderItemRepository,
        private readonly SectionProviderInterface $sectionProvider,
    ) {
    }

    /** @param array<array-key, mixed> $context */
    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return
            is_a($resourceClass, AdjustmentInterface::class, true) &&
            $this->sectionProvider->getSection() instanceof AdminApiSection &&
            is_a(array_key_first($context['subresource_resources']), OrderItemInterface::class, true)
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
        $orderItem = $this->orderItemRepository->find(reset($context['subresource_identifiers']));
        if (null === $orderItem) {
            return [];
        }

        return $orderItem->getAdjustmentsRecursively();
    }
}
