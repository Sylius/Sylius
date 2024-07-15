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

namespace Sylius\Bundle\ApiBundle\Serializer\Normalizer;

use ApiPlatform\Metadata\HttpOperation;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webmozart\Assert\Assert;

final class ShippingMethodNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'sylius_shipping_method_normalizer_already_called';

    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private OrderRepositoryInterface $orderRepository,
        private ShipmentRepositoryInterface $shipmentRepository,
        private ServiceRegistryInterface $shippingCalculators,
        private RequestStack $requestStack,
    ) {
    }

    public function normalize($object, $format = null, array $context = [])
    {
        Assert::isInstanceOf($object, ShippingMethodInterface::class);
        Assert::keyNotExists($context, self::ALREADY_CALLED);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);

        $context[self::ALREADY_CALLED] = true;

        $request = $this->requestStack->getCurrentRequest();
        $tokenValue = $request->attributes->get('tokenValue');
        $shipmentId = $request->attributes->get('shipmentId');
        if ($tokenValue === null || $shipmentId === null) {
            return null;
        }

        /** @var ChannelInterface $channel */
        $channel = $context['sylius_api_channel'];

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValueAndChannel($tokenValue, $channel);
        Assert::notNull($cart);

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->findOneByOrderId($shipmentId, $cart->getId());
        Assert::notNull($shipment);
        Assert::true($cart->hasShipment($shipment), 'Shipment doesn\'t match for order');

        $calculator = $this->shippingCalculators->get($object->getCalculator());

        $data = $this->normalizer->normalize($object, $format, $context);
        $data['price'] = $calculator->calculate($shipment, $object->getConfiguration());

        return $data;
    }

    public function supportsNormalization($data, $format = null, $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) {
            return false;
        }

        /** @var HttpOperation|null $operation */
        $operation = $context['root_operation'] ?? null;

        return
            $data instanceof ShippingMethodInterface &&
            $this->sectionProvider->getSection() instanceof ShopApiSection &&
            $operation instanceof HttpOperation &&
            isset($operation->getUriVariables()['tokenValue']) &&
            isset($operation->getUriVariables()['shipmentId'])
        ;
    }
}
