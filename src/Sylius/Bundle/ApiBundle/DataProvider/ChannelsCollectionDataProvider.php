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

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private ChannelContextInterface $channelContext)
    {
    }

    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): array
    {
        return [$this->channelContext->getChannel()];
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ChannelInterface::class, true) && $this->isShopGetCollectionOperation($context);
    }

    private function isShopGetCollectionOperation(array $context): bool
    {
        return isset($context['collection_operation_name']) && \str_starts_with($context['collection_operation_name'], 'shop');
    }
}
