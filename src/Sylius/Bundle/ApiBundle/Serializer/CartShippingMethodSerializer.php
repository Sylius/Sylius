<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Serializer;

use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class CartShippingMethodSerializer implements ContextAwareNormalizerInterface
{
    /** @var NormalizerInterface */
    private $objectNormalizer;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var ShippingMethodsResolverInterface */
    private $shippingMethodsResolver;

    /** @var ServiceRegistryInterface */
    private $calculators;

    public function __construct(
        NormalizerInterface $objectNormalizer,
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        ServiceRegistryInterface $calculators
    ) {
        $this->objectNormalizer = $objectNormalizer;
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->calculators = $calculators;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        $data = $this->objectNormalizer->normalize($object, $format, $context);

        $subresourceIdentifiers = $context['subresource_identifiers'];

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($subresourceIdentifiers['id']);
        Assert::notNull($cart);

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($subresourceIdentifiers['shipments']);
        Assert::notNull($shipment);

        Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order');

        $shippingMethod = $shipment->getMethod();
        $calculator = $this->calculators->get($shippingMethod->getCalculator());

        /** @var int $price */
        $data['price'] = $calculator->calculate($shipment, $shippingMethod->getConfiguration());

        return $this->getCartShippingMethods($cart, $shipment);
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return $data instanceof ShippingMethodInterface && isset($subresourceIdentifiers['id'], $subresourceIdentifiers['shipments']);
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
            /** @var int $price */
            $price = $calculator->calculate($shipment, $shippingMethod->getConfiguration());

            $cartShippingMethods[] = [
                'shippingMethod' => $this->objectNormalizer->normalize($shippingMethod),
                'price' => $price
            ];
        }

        return $cartShippingMethods;
    }
}
