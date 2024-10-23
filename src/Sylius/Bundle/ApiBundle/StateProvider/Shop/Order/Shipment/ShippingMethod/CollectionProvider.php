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

namespace Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Shipment\ShippingMethod;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\Serializer\ContextKeys;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\Resolver\ShippingMethodsResolverInterface;
use Webmozart\Assert\Assert;

/** @implements ProviderInterface<ShippingMethodInterface> */
final readonly class CollectionProvider implements ProviderInterface
{
    public function __construct(
        private SectionProviderInterface $sectionProvider,
        private ShipmentRepositoryInterface $shipmentRepository,
        private ShippingMethodsResolverInterface $shippingMethodsResolver,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        Assert::true(is_a($operation->getClass(), ShippingMethodInterface::class, true));
        Assert::isInstanceOf($operation, GetCollection::class);
        Assert::isInstanceOf($this->sectionProvider->getSection(), ShopApiSection::class);
        Assert::keyExists($uriVariables, 'tokenValue');
        Assert::keyExists($uriVariables, 'shipmentId');

        /** @var ChannelInterface $channel */
        $channel = $context[ContextKeys::CHANNEL];

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->findOneByOrderTokenAndChannel(
            $uriVariables['shipmentId'],
            $uriVariables['tokenValue'],
            $channel,
        );

        if ($shipment === null) {
            return [];
        }

        return $this->shippingMethodsResolver->getSupportedMethods($shipment);
    }
}
