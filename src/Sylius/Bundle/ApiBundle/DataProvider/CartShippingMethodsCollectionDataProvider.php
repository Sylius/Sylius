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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\UserRepository;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
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

    private UserContextInterface $userContext;

    private OrderInterface $order;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        ShippingMethodRepositoryInterface $shippingMethodsRepository,
        ShippingMethodsResolverInterface $shippingMethodsResolver,
        UserContextInterface $userContext,
        OrderInterface $order,
    ) {
        $this->orderRepository = $orderRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->shippingMethodsRepository = $shippingMethodsRepository;
        $this->shippingMethodsResolver = $shippingMethodsResolver;
        $this->userContext = $userContext;
        $this->order = $order;
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): array
    {
        if (!isset($context['filters'])) {
            $channel = $this->order->getChannel();

            return $this->shippingMethodsRepository->findEnabledForChannel($channel); // Find by channel
        }

        $parameters = $context['filters'];

        Assert::keyExists($parameters, 'tokenValue');
        Assert::keyExists($parameters, 'shipmentId');

        $user = $this->userContext->getUser();

        /** @var CustomerInterface|null $customer */
        $customer = $user instanceof ShopUserInterface ? $user->getCustomer() : null;

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValueAndCustomer($parameters['tokenValue'], $customer);// Search for cart by token & user is null || cart by token & user
        if ($cart->isEmpty()) { // return empty array if cart not found
            return [];
        }

        /** @var ShipmentInterface|null $shipment */
        $shipment = $this->shipmentRepository->find($parameters['shipmentId']);  // Search for shipment by cart and shipment id
        if ($shipment === null) { // return empty array if shipment not found
            return [];
        }

        return $this->shippingMethodsResolver->getSupportedMethods($shipment);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, ShippingMethodInterface::class, true); // only for shop context
    }
}
