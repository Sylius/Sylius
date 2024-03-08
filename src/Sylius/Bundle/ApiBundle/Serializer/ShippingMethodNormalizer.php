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

namespace Sylius\Bundle\ApiBundle\Serializer;

use ApiPlatform\Core\Util\RequestParser;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

final class ShippingMethodNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_shipping_method_normalizer_already_called';

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ShipmentRepositoryInterface $shipmentRepository,
        private ServiceRegistryInterface $shippingCalculators,
        private RequestStack $requestStack,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ShippingMethodInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $request = $this->requestStack->getCurrentRequest();

        $filters = $request->attributes->get('_api_filters');
        if (null === $filters) {
            $queryString = RequestParser::getQueryString($request);
            $filters = $queryString ? RequestParser::parseRequestParams($queryString) : null;
        }

        $data = $this->normalizer->normalize($object, $format, $context);

        if (null === $filters) {
            return $data;
        }

        if (!isset($filters['tokenValue']) || !isset($filters['shipmentId'])) {
            return null;
        }

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValueAndChannel($filters['tokenValue'], $channel);

        Assert::notNull($cart);

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->findOneByOrderId($filters['shipmentId'], $cart->getId());

        Assert::notNull($shipment);

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
