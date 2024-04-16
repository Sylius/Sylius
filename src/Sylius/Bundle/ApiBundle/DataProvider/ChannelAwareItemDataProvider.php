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
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;

final class ChannelAwareItemDataProvider implements ItemDataProviderInterface
{
    public function __construct(
        private ItemDataProviderInterface $itemDataProvider,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function getItem(string $resourceClass, $id, ?string $operationName = null, array $context = []): ?object
    {
        return $this->itemDataProvider->getItem($resourceClass, $id, $operationName, $this->processContext($context));
    }

    private function processContext(array $context): array
    {
        if (array_key_exists(ContextKeys::CHANNEL, $context)) {
            return $context;
        }

        try {
            $context[ContextKeys::CHANNEL] = $this->channelContext->getChannel();
        } catch (ChannelNotFoundException) {
        }

        return $context;
    }
}
