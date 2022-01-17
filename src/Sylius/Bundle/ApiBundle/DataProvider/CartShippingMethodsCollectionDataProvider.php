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
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Routing\RouterInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CartShippingMethodsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private OrderRepositoryInterface $orderRepository;

    private ShipmentRepositoryInterface $shipmentRepository;

    private ShippingMethodRepositoryInterface $shippingMethodsRepository;

    private ShippingMethodsResolverInterface $shippingMethodsResolver;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodsRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodsRepository = $shippingMethodsRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        if (!isset($context['filters'])) {
            return $this->shippingMethodsRepository->findAll(); // Find by channel
        }

        $parameters = $context['filters'];

        Assert::keyExists($context['filters'], 'tokenValue');
        Assert::keyExists($context['filters'], 'shipmentId');

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($parameters['tokenValue']); // Search for cart by token & user is null || cart by token & user
        Assert::notNull($cart); // return empty array if cart not found

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($parameters['shipmentId']);  // Search for shipment by cart and shipment id
        Assert::notNull($shipment);  // return empty array if shipment not found

        Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order'); // won't be needed

        return $this->shippingMethodsResolver->getSupportedMethods($shipment);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ShippingMethodInterface::class, true); // only for shop context
    }
}
