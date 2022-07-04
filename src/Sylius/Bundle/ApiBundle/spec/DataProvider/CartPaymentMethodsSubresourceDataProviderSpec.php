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

namespace spec\Sylius\Bundle\ApiBundle\DataProvider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Symfony\Component\HttpFoundation\Request;

final class CartPaymentMethodsSubresourceDataProviderSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $this->beConstructedWith($orderRepository, $paymentRepository, $paymentMethodsResolver);
    }

    function it_supports_only_order_payment_methods_subresource_data_provider_with_id_and_payments_subresource_identifiers(): void
    {
        $this
            ->supports(ProductInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $this
            ->supports(PaymentMethodInterface::class, Request::METHOD_GET)
            ->shouldReturn(false)
        ;

        $context['subresource_identifiers'] = ['tokenValue' => '69', 'payments' => '420'];

        $this
            ->supports(
                PaymentMethodInterface::class,
                Request::METHOD_GET,
                $context,
            )
            ->shouldReturn(true)
        ;
    }

    function it_throws_an_exception_if_cart_does_not_exist(
        OrderRepositoryInterface $orderRepository,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '69', 'payments' => '420'];

        $orderRepository->findCartByTokenValue($context['subresource_identifiers']['tokenValue'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                PaymentMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_throws_an_exception_if_payment_does_not_exist(
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '69', 'payments' => '420'];

        $orderRepository->findCartByTokenValue($context['subresource_identifiers']['tokenValue'])->willReturn($order);
        $paymentRepository->find($context['subresource_identifiers']['payments'])->willReturn($payment);

        $order->hasPayment($payment)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                PaymentMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_throws_an_exception_if_payment_does_not_match_order(
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '69', 'payments' => '420'];

        $orderRepository->findCartByTokenValue($context['subresource_identifiers']['tokenValue'])->willReturn($order);
        $paymentRepository->find($context['subresource_identifiers']['payments'])->willReturn($payment);

        $order->hasPayment($payment)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('getSubresource', [
                PaymentMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_returns_an_exception_if_cart_does_not_have_payments(
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        OrderInterface $order,
        PaymentInterface $payment,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '69', 'payments' => '420'];

        $orderRepository->findCartByTokenValue($context['subresource_identifiers']['tokenValue'])->willReturn($order);
        $paymentRepository->find($context['subresource_identifiers']['payments'])->willReturn($payment);

        $order->hasPayment($payment)->willReturn(false);

        $this
            ->shouldThrow(\Exception::class)
            ->during('getSubresource', [
                PaymentMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            ])
        ;
    }

    function it_returns_order_payment_methods(
        OrderRepositoryInterface $orderRepository,
        PaymentRepositoryInterface $paymentRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        OrderInterface $order,
        PaymentInterface $payment,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $context['subresource_identifiers'] = ['tokenValue' => '69', 'payments' => '420'];

        $orderRepository->findCartByTokenValue($context['subresource_identifiers']['tokenValue'])->willReturn($order);
        $paymentRepository->find($context['subresource_identifiers']['payments'])->willReturn($payment);

        $order->hasPayment($payment)->willReturn(true);
        $order->hasPayments()->willReturn(true);

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$paymentMethod]);

        $this
            ->getSubresource(
                PaymentMethodInterface::class,
                [],
                $context,
                Request::METHOD_GET,
            )
            ->shouldReturn([$paymentMethod])
        ;
    }
}
