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

namespace spec\Sylius\Bundle\ApiBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class TokenValueBasedCartContextSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, OrderRepositoryInterface $orderRepository): void
    {
        $this->beConstructedWith($requestStack, $orderRepository, '/api/v2');
    }

    function it_implements_cart_context_interface(): void
    {
        $this->shouldImplement(CartContextInterface::class);
    }

    function it_returns_cart_by_token_value(
        RequestStack $requestStack,
        OrderRepositoryInterface $orderRepository,
        Request $request,
        OrderInterface $cart,
    ): void {
        $request->attributes = new ParameterBag(['tokenValue' => 'TOKEN_VALUE']);
        $request->getRequestUri()->willReturn('/api/v2/orders/TOKEN_VALUE');

        $requestStack->getMainRequest()->willReturn($request);
        $orderRepository->findCartByTokenValue('TOKEN_VALUE')->willReturn($cart);

        $this->getCart()->shouldReturn($cart);
    }

    function it_throws_an_exception_if_there_is_no_master_request_on_request_stack(RequestStack $requestStack): void
    {
        $requestStack->getMainRequest()->willReturn(null);

        $this
            ->shouldThrow(new CartNotFoundException('There is no main request on request stack.'))
            ->during('getCart')
        ;
    }

    function it_throws_an_exception_if_the_request_is_not_an_api_request(
        RequestStack $requestStack,
        Request $request,
    ): void {
        $request->attributes = new ParameterBag([]);
        $request->getRequestUri()->willReturn('/orders');

        $requestStack->getMainRequest()->willReturn($request);

        $this
            ->shouldThrow(new CartNotFoundException('The main request is not an API request.'))
            ->during('getCart')
        ;
    }

    function it_throws_an_exception_if_there_is_no_token_value(
        RequestStack $requestStack,
        Request $request,
    ): void {
        $request->attributes = new ParameterBag([]);
        $request->getRequestUri()->willReturn('/api/v2/orders');

        $requestStack->getMainRequest()->willReturn($request);

        $this
            ->shouldThrow(new CartNotFoundException('Sylius was not able to find the cart, as there is no passed token value.'))
            ->during('getCart')
        ;
    }

    function it_throws_an_exception_if_there_is_no_cart_with_given_token_value(
        RequestStack $requestStack,
        OrderRepositoryInterface $orderRepository,
        Request $request,
    ): void {
        $request->attributes = new ParameterBag(['tokenValue' => 'TOKEN_VALUE']);
        $request->getRequestUri()->willReturn('/api/v2/orders/TOKEN_VALUE');

        $requestStack->getMainRequest()->willReturn($request);
        $orderRepository->findCartByTokenValue('TOKEN_VALUE')->willReturn(null);

        $this
            ->shouldThrow(new CartNotFoundException('Sylius was not able to find the cart for passed token value.'))
            ->during('getCart')
        ;
    }
}
