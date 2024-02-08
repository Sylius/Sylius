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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

final class PaymentMethodsCollectionDataProviderSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentRepositoryInterface $paymentRepository,
        ChannelContextInterface $channelContext,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $paymentMethodRepository,
            $paymentRepository,
            $channelContext,
            $paymentMethodsResolver,
        );
    }

    function it_supports_only_get_collection_payment_method_in_shop_context(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $this
            ->supports(PaymentMethodInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $context['collection_operation_name'] = 'shop_get';

        $this
            ->supports(PaymentMethodInterface::class, Request::METHOD_GET, $context)
            ->shouldReturn(true)
        ;
    }

    function it_returns_all_enabled_for_channel_payment_methods_if_filters_are_not_specified(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        PaymentMethodRepositoryInterface $paymentMethodRepository,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $paymentMethodRepository
            ->findEnabledForChannel($channel)
            ->willReturn([$firstPaymentMethod->getWrappedObject(), $secondPaymentMethod->getWrappedObject()])
        ;

        $this
            ->getCollection(PaymentMethodInterface::class, null, [])
            ->shouldReturn([$firstPaymentMethod->getWrappedObject(), $secondPaymentMethod->getWrappedObject()])
        ;
    }

    function it_returns_empty_array_when_user_provide_only_token_value(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $paymentMethodsResolver->getSupportedMethods(Argument::any())->shouldNotBeCalled();

        $context = [
            'filters' => [
                'tokenValue' => 'CART_TOKEN_VALUE',
            ],
        ];

        $this->getCollection(PaymentMethodInterface::class, null, $context)->shouldReturn([]);
    }

    function it_returns_empty_array_when_user_provide_only_payment_id(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $paymentMethodsResolver->getSupportedMethods(Argument::any())->shouldNotBeCalled();

        $context = [
            'filters' => [
                'paymentId' => '123',
            ],
        ];

        $this->getCollection(PaymentMethodInterface::class, null, $context)->shouldReturn([]);
    }

    function it_returns_empty_array_if_cart_is_not_found_in_channel(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        OrderRepositoryInterface $orderRepository,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findCartByTokenValueAndChannel('CART_TOKEN_VALUE', $channel)->willReturn(null);

        $paymentMethodsResolver->getSupportedMethods(Argument::any())->shouldNotBeCalled();

        $context = [
            'filters' => [
                'tokenValue' => 'CART_TOKEN_VALUE',
                'paymentId' => '123',
            ],
        ];

        $this->getCollection(PaymentMethodInterface::class, null, $context)->shouldReturn([]);
    }

    function it_returns_empty_array_if_payment_with_order_is_not_found(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        PaymentRepositoryInterface $paymentRepository,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findCartByTokenValueAndChannel('CART_TOKEN_VALUE', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');

        $paymentRepository->findOneByOrderId('123', '111')->willReturn(null);

        $paymentMethodsResolver->getSupportedMethods(Argument::any())->shouldNotBeCalled();

        $context = [
            'filters' => [
                'tokenValue' => 'CART_TOKEN_VALUE',
                'paymentId' => '123',
            ],
        ];

        $this->getCollection(PaymentMethodInterface::class, null, $context)->shouldReturn([]);
    }

    function it_provides_array_of_payment_methods_available_to_provided_payment_with_cart(
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        OrderRepositoryInterface $orderRepository,
        OrderInterface $cart,
        PaymentRepositoryInterface $paymentRepository,
        PaymentInterface $payment,
        PaymentMethodInterface $firstPaymentMethod,
        PaymentMethodInterface $secondPaymentMethod,
    ): void {
        $channelContext->getChannel()->willReturn($channel);

        $orderRepository->findCartByTokenValueAndChannel('CART_TOKEN_VALUE', $channel)->willReturn($cart);
        $cart->getId()->willReturn('111');

        $paymentRepository->findOneByOrderId('123', '111')->willReturn($payment);

        $paymentMethodsResolver
            ->getSupportedMethods($payment)
            ->willReturn([$firstPaymentMethod->getWrappedObject(), $secondPaymentMethod->getWrappedObject()])
        ;

        $context = [
            'filters' => [
                'tokenValue' => 'CART_TOKEN_VALUE',
                'paymentId' => '123',
            ],
        ];

        $this
            ->getCollection(PaymentMethodInterface::class, null, $context)
            ->shouldReturn([$firstPaymentMethod->getWrappedObject(), $secondPaymentMethod->getWrappedObject()])
        ;
    }
}
