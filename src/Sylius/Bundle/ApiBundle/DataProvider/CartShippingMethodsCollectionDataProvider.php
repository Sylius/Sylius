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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CartShippingMethodsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private OrderRepositoryInterface $orderRepository;

    private ShipmentRepositoryInterface $shipmentRepository;

    private ShippingMethodRepositoryInterface $shippingMethodsRepository;

    private ShippingMethodsResolverInterface $shippingMethodsResolver;

    private ChannelContextInterface $channelContext;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodsRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ChannelContextInterface $channelContext
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodsRepository = $shippingMethodsRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->channelContext= $channelContext;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        if (!isset($context['filters'])) {
            /** @var ChannelInterface $channel */
            $channel = $this->channelContext->getChannel();

            return $this->shippingMethodsRepository->findEnabledForChannel($channel);
        }

        $parameters = $context['filters'];

        Assert::keyExists($parameters, 'tokenValue');
        Assert::keyExists($parameters, 'shipmentId');

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValueAndChannel($parameters['tokenValue'], $channel);
        if ($cart === null) {
            return [];
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->findOneByOrderId($parameters['shipmentId'], $cart->getId());
        if ($shipment === null) {
            return [];
        }

        return $this->shippingMethodsResolver->getSupportedMethods($shipment);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ShippingMethodInterface::class, true); // only for shop context
    }
}
