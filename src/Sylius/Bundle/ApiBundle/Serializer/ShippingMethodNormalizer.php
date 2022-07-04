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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Webmozart\Assert\Assert;

/** @experimental */
final class ShippingMethodNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_shipping_method_normalizer_already_called';

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ShipmentRepositoryInterface $shipmentRepository,
        private ServiceRegistryInterface $shippingCalculators,
    ) {
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ShippingMethodInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);

        $context[self::ALREADY_CALLED] = true;

        $subresourceIdentifiers = $context['subresource_identifiers'];

        $shipmentId = $subresourceIdentifiers['shipments'] ?? $subresourceIdentifiers['id'];

        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->find($shipmentId);

        Assert::notNull($shipment);

        if (isset($subresourceIdentifiers['tokenValue'])) {
            /** @var OrderInterface|null $cart */
            $cart = $this->orderRepository->findCartByTokenValue($subresourceIdentifiers['tokenValue']);
            Assert::notNull($cart);

            Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order');
        }

        $data = $this->normalizer->normalize($object, $format, $context);

        $calculator = $this->shippingCalculators->get($object->getCalculator());
        $data['price'] = $calculator->calculate($shipment, $object->getConfiguration());

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            $data instanceof ShippingMethodInterface &&
            $this->isNotAdminGetOperation($context) &&
            (
                isset($subresourceIdentifiers['tokenValue'], $subresourceIdentifiers['shipments']) ||
                isset($subresourceIdentifiers['id'])
            )
        ;
    }

    private function isNotAdminGetOperation(array $context): bool
    {
        return !isset($context['item_operation_name']) || !($context['item_operation_name'] === 'admin_get');
    }
}
