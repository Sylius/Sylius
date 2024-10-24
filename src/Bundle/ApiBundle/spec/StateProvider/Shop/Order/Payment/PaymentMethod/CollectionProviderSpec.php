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

namespace spec\Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Payment\PaymentMethod;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class CollectionProviderSpec extends ObjectBehavior
{
    function let(
        PaymentRepositoryInterface $paymentRepository,
        OrderRepositoryInterface $orderRepository,
        SectionProviderInterface $sectionProvider,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
    ): void {
        $this->beConstructedWith($paymentRepository, $orderRepository, $sectionProvider, $paymentMethodsResolver);
    }

    function it_provides_payment_methods(
        SectionProviderInterface $sectionProvider,
        PaymentRepositoryInterface $paymentRepository,
        OrderRepositoryInterface $orderRepository,
        PaymentMethodsResolverInterface $paymentMethodsResolver,
        ChannelInterface $channel,
        OrderInterface $cart,
        PaymentInterface $payment,
        PaymentMethodInterface $method,
    ): void {
        $operation = new GetCollection(class: PaymentMethodInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn($cart);
        $cart->getTokenValue()->willReturn('TOKEN');
        $paymentRepository->findOneByOrderToken(1, 'TOKEN')->willReturn($payment);

        $paymentMethodsResolver->getSupportedMethods($payment)->willReturn([$method->getWrappedObject()]);

        $this
            ->provide($operation, ['tokenValue' => 'TOKEN', 'paymentId' => 1], ['sylius_api_channel' => $channel])
            ->shouldReturn([$method->getWrappedObject()])
        ;
    }

    function it_returns_empty_array_if_cart_does_not_exist(
        SectionProviderInterface $sectionProvider,
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
    ): void {
        $operation = new GetCollection(class: PaymentMethodInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn(null);

        $this->provide($operation, ['tokenValue' => 'TOKEN', 'paymentId' => 1], ['sylius_api_channel' => $channel])->shouldReturn([]);
    }

    function it_returns_empty_array_if_payment_does_not_exist(
        SectionProviderInterface $sectionProvider,
        PaymentRepositoryInterface $paymentRepository,
        OrderRepositoryInterface $orderRepository,
        ChannelInterface $channel,
        OrderInterface $cart,
    ): void {
        $operation = new GetCollection(class: PaymentMethodInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $orderRepository->findCartByTokenValueAndChannel('TOKEN', $channel)->willReturn($cart);
        $cart->getTokenValue()->willReturn('TOKEN');
        $paymentRepository->findOneByOrderToken(1, 'TOKEN')->willReturn(null);

        $this->provide($operation, ['tokenValue' => 'TOKEN', 'paymentId' => 1], ['sylius_api_channel' => $channel])->shouldReturn([]);
    }

    function it_throws_an_exception_when_resource_is_not_a_payment_method_interface(): void
    {
        $operation = new GetCollection(class: \stdClass::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_get_collection(): void
    {
        $operation = new Get(class: PaymentMethodInterface::class);

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_operation_is_not_in_shop_api_section(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: PaymentMethodInterface::class);
        $sectionProvider->getSection()->willReturn(new AdminApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }

    function it_throws_an_exception_when_uri_variables_do_not_exist(
        SectionProviderInterface $sectionProvider,
    ): void {
        $operation = new GetCollection(class: PaymentMethodInterface::class);
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $this->shouldThrow(\InvalidArgumentException::class)->during('provide', [$operation]);
    }
}
