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

namespace Tests\Sylius\Bundle\CoreBundle\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutResolver;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class CheckoutResolverTest extends TestCase
{
    private CartContextInterface&MockObject $cartContext;

    private CheckoutStateUrlGeneratorInterface&MockObject $urlGenerator;

    private MockObject&RequestMatcherInterface $requestMatcher;

    private MockObject&StateMachineInterface $stateMachine;

    private CheckoutResolver $checkoutResolver;

    protected function setUp(): void
    {
        $this->cartContext = $this->createMock(CartContextInterface::class);
        $this->urlGenerator = $this->createMock(CheckoutStateUrlGeneratorInterface::class);
        $this->requestMatcher = $this->createMock(RequestMatcherInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->checkoutResolver = new CheckoutResolver($this->cartContext, $this->urlGenerator, $this->requestMatcher, $this->stateMachine);
    }

    public function testOnlyAppliesToTheMainRequest(): void
    {
        $event = $this->createMock(RequestEvent::class);

        $event->expects($this->once())->method('isMainRequest')->willReturn(false);
        $event->expects($this->never())->method('getRequest');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testOnlyAppliesToAMatchedRequest(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(false)
        ;

        $this->cartContext->expects($this->never())->method('getCart');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testRedirectsWhenOrderHasNoItems(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $order = $this->createMock(OrderInterface::class);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(true);

        $this->urlGenerator
            ->expects($this->once())
            ->method('generateForCart')
            ->willReturn('/target-url')
        ;

        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(RedirectResponse::class))
        ;

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testDoesNothingWhenThereIsNoSyliusRequestAttribute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $request = new Request();

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->stateMachine->expects($this->never())->method('can');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testDoesNothingWhenThereIsNoStateMachineRequestAttribute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $request = new Request([], [], ['_sylius' => []]);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->stateMachine->expects($this->never())->method('can');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testDoesNothingWhenThereIsNoStateMachineGraphRequestAttribute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $request = new Request([], [], ['_sylius' => ['state_machine' => ['transition' => 'test_transition']]]);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->stateMachine->expects($this->never())->method('can');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testDoesNothingWhenThereIsNoStateMachineTransitionRequestAttribute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $request = new Request([], [], ['_sylius' => ['state_machine' => ['graph' => 'test_graph']]]);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(false);
        $this->stateMachine->expects($this->never())->method('can');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testDoesNothingWhenTheRequestedTransitionCanBeApplied(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $request = new Request([], [], [
            '_sylius' => ['state_machine' => ['graph' => 'test_graph', 'transition' => 'test_transition']],
        ]);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(false);

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($order, 'test_graph', 'test_transition')
            ->willReturn(true)
        ;

        $event->expects($this->never())->method('setResponse');

        $this->checkoutResolver->onKernelRequest($event);
    }

    public function testRedirectsWhenTheRequestedTransitionCannotBeApplied(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $order = $this->createMock(OrderInterface::class);

        $request = new Request([], [], [
            '_sylius' => ['state_machine' => ['graph' => 'test_graph', 'transition' => 'test_transition']],
        ]);

        $event->expects($this->once())->method('isMainRequest')->willReturn(true);
        $event->expects($this->once())->method('getRequest')->willReturn($request);

        $this->requestMatcher
            ->expects($this->once())
            ->method('matches')
            ->with($request)
            ->willReturn(true)
        ;

        $this->cartContext->expects($this->once())->method('getCart')->willReturn($order);
        $order->expects($this->once())->method('isEmpty')->willReturn(false);

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($order, 'test_graph', 'test_transition')
            ->willReturn(false)
        ;

        $this->urlGenerator
            ->expects($this->once())
            ->method('generateForOrderCheckoutState')
            ->with($order)
            ->willReturn('/target-url')
        ;

        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf(RedirectResponse::class))
        ;

        $this->checkoutResolver->onKernelRequest($event);
    }
}
