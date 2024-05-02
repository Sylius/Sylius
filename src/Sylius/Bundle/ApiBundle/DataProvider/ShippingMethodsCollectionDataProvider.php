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
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;

final class ShippingMethodsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private ShipmentRepositoryInterface $shipmentRepository,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
        private ShippingMethodsResolverInterface $shippingMethodsResolver,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = []): array
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if (!isset($context['filters'])) {
            return $this->shippingMethodRepository->findEnabledForChannel($channel);
        }

        $parameters = $context['filters'];

        if (!isset($parameters['tokenValue']) || !isset($parameters['shipmentId'])) {
            return [];
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->findOneByOrderTokenAndChannel($parameters['shipmentId'], $parameters['tokenValue'], $channel);
        if ($shipment === null) {
            return [];
        }

        return $this->shippingMethodsResolver->getSupportedMethods($shipment);
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ShippingMethodInterface::class, true) && $this->isShopGetCollectionOperation($context);
    }

    private function isShopGetCollectionOperation(array $context): bool
    {
        return isset($context['collection_operation_name']) && \str_starts_with($context['collection_operation_name'], 'shop');
    }
}
