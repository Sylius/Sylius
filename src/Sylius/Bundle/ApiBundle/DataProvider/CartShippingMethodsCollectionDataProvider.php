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

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\OperationDataProviderTrait;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\InvalidIdentifierException;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use ApiPlatform\Core\Util\AttributesExtractor;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CartShippingMethodsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private OrderRepositoryInterface $orderRepository;
    private ShipmentRepositoryInterface $shipmentRepository;
    private ShippingMethodsResolverInterface $shippingMethodsResolver;
    private RouterInterface $router;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        RouterInterface $router
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->router = $router;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        $parameters = $this->router->match($context['request_uri']);

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($parameters['tokenValue']);
        Assert::notNull($cart);

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($parameters['shipments']);
        Assert::notNull($shipment);

        Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order');

        return $this->shippingMethodsResolver->getSupportedMethods($shipment);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ShippingMethodInterface::class, true);
    }
}
