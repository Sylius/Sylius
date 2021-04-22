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

use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\DataProvider\SubresourceDataProviderInterface;
use Sylius\Bundle\ApiBundle\View\Factory\CartShippingMethodFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CartShippingMethodsSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var ShippingMethodsResolverInterface */
    private $shippingMethodsResolver;

    /** @var ServiceRegistryInterface */
    private $calculators;

    /** @var CartShippingMethodFactoryInterface */
    private $cartShippingMethodFactory;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ServiceRegistryInterface $calculators,
        CartShippingMethodFactoryInterface $cartShippingMethodFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->calculators = $calculators;
        $this->cartShippingMethodFactory = $cartShippingMethodFactory;
    }

    public function getSubresource(string $resourceClass, array $identifiers, array $context, string $operationName = null)
    {
        $subresourceIdentifiers = $context['subresource_identifiers'];

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($subresourceIdentifiers['tokenValue']);
        Assert::notNull($cart);

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($subresourceIdentifiers['shipments']);
        Assert::notNull($shipment);

        Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order');

        return $this->getCartShippingMethods($cart, $shipment);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            is_a($resourceClass, ShippingMethodInterface::class, true) &&
            isset($subresourceIdentifiers['tokenValue'], $subresourceIdentifiers['shipments'])
        ;
    }

    private function getCartShippingMethods(OrderInterface $cart, ShipmentInterface $shipment): array
    {
        if (!$cart->hasShipments()) {
            return [];
        }

        $cartShippingMethods = [];

        $shippingMethods = $this->shippingMethodsResolver->getSupportedMethods($shipment);

        /** @var ShippingMethodInterface $shippingMethod */
        foreach ($shippingMethods as $shippingMethod) {
            $calculator = $this->calculators->get($shippingMethod->getCalculator());
            /** @var int $cost */
            $cost = $calculator->calculate($shipment, $shippingMethod->getConfiguration());

            $cartShippingMethods[] = $this->cartShippingMethodFactory->create(
                $shippingMethod,
                $cost
            );
        }

        return $cartShippingMethods;
    }
}
