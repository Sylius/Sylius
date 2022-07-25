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
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class CartPaymentMethodsSubresourceDataProvider implements RestrictedDataProviderInterface, SubresourceDataProviderInterface
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentMethodsResolverInterface $paymentMethodsResolver,
    ) {
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        $subresourceIdentifiers = $context['subresource_identifiers'] ?? null;

        return
            is_a($resourceClass, PaymentMethodInterface::class, true) &&
            isset($subresourceIdentifiers['tokenValue'], $subresourceIdentifiers['payments'])
        ;
    }

    public function getSubresource(string $resourceClass, array $identifiers, array $context, string $operationName = null)
    {
        $subresourceIdentifiers = $context['subresource_identifiers'];

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findCartByTokenValue($subresourceIdentifiers['tokenValue']);
        Assert::notNull($order);

        /** @var PaymentInterface|null $payment */
        $payment = $this->paymentRepository->find($subresourceIdentifiers['payments']);
        Assert::notNull($payment);

        Assert::true($order->hasPayment($payment), 'Payment doesn\'t match for order');

        return $this->paymentMethodsResolver->getSupportedMethods($payment);
    }
}
