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

namespace Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\Util\RequestParser;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShippingMethodNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_shipping_method_normalizer_already_called';

    private OrderRepositoryInterface $orderRepository;

    private ShipmentRepositoryInterface $shipmentRepository;

    private ServiceRegistryInterface $shippingCalculators;

    private RequestStack $requestStack;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ServiceRegistryInterface $shippingCalculators,
        RequestStack $requestStack
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingCalculators = $shippingCalculators;
        $this->requestStack = $requestStack;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ShippingMethodInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $request = $this->requestStack->getCurrentRequest();

        if (null === $filters = $request->attributes->get('_api_filters')) {
            $queryString = RequestParser::getQueryString($request);
            $filters = $queryString ? RequestParser::parseRequestParams($queryString) : null;
        }

        $data = $this->normalizer->normalize($object, $format, $context);

        if (!isset($filters)) {
            return $data;
        }

        Assert::keyExists($filters, 'tokenValue');
        Assert::keyExists($filters, 'shipmentId');

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($filters['shipmentId']); // Duplication of logic from CartShippingMethodCollectionDataProvider

        Assert::notNull($shipment);

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($filters['tokenValue']);
        Assert::notNull($cart);

        Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order');


        $calculator = $this->shippingCalculators->get($object->getCalculator());
        $data['price'] = $calculator->calculate($shipment, $object->getConfiguration());

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof ShippingMethodInterface && $this->isShopGetCollectionOperation($context);
    }

    private function isShopGetCollectionOperation(array $context): bool
    {
        return isset($context['collection_operation_name']) && \str_starts_with($context['collection_operation_name'], 'shop');
    }
}
