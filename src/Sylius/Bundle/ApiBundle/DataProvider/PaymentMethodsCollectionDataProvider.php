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

namespace Sylius\Bundle\ApiBundle\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class PaymentMethodsCollectionDataProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private ChannelContextInterface $channelContext,
        private PaymentMethodsResolverInterface $paymentMethodsResolver,
    ) {
    }

    public function getCollection(string $resourceClass, ?string $operationName = null, array $context = [])
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        if (!isset($context['filters'])) {
            return $this->paymentMethodRepository->findEnabledForChannel($channel);
        }

        $parameters = $context['filters'];

        if (!array_key_exists('tokenValue', $parameters) || !array_key_exists('paymentId', $parameters)) {
            return [];
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValueAndChannel($parameters['tokenValue'], $channel);
        if ($cart === null) {
            return [];
        }

        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->findOneByOrderId($parameters['paymentId'], $cart->getId());
        if ($payment === null) {
            return [];
        }

        return $this->paymentMethodsResolver->getSupportedMethods($payment);
    }

    public function supports(string $resourceClass, ?string $operationName = null, array $context = []): bool
    {
        return is_a($resourceClass, PaymentMethodInterface::class, true) && $this->isShopGetCollectionOperation($context);
    }

    private function isShopGetCollectionOperation(array $context): bool
    {
        return isset($context['collection_operation_name']) && \str_starts_with($context['collection_operation_name'], 'shop');
    }
}
