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

namespace spec\Sylius\Bundle\CoreBundle\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class CheckoutResolverSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        CheckoutStateUrlGeneratorInterface $urlGenerator,
        RequestMatcherInterface $requestMatcher,
        FactoryInterface $stateMachineFactory,
    ): void {
        $this->beConstructedWith(
            $cartContext,
            $urlGenerator,
            $requestMatcher,
            $stateMachineFactory,
        );
    }

    function it_only_applies_to_the_main_request(RequestEvent $event): void
    {
        $event->isMainRequest()->willReturn(false);
        $event->getRequest()->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_only_applies_to_a_matched_request(
        RequestEvent $event,
        Request $request,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(false);
        $cartContext->getCart()->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_redirects_when_order_has_no_items(
        RequestEvent $event,
        Request $request,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        CheckoutStateUrlGeneratorInterface $urlGenerator,
    ): void {
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(true);
        $urlGenerator->generateForCart()->willReturn('/target-url');
        $event->setResponse(Argument::type(RedirectResponse::class))->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_nothing_when_there_is_no_sylius_request_attribute(
        RequestEvent $event,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
    ): void {
        $request = new Request();
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $stateMachineFactory->get($order, Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_nothing_when_there_is_no_state_machine_request_attribute(
        RequestEvent $event,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
    ): void {
        $request = new Request([], [], ['_sylius' => []]);
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $stateMachineFactory->get($order, Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_nothing_when_there_is_no_state_machine_graph_request_attribute(
        RequestEvent $event,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
    ): void {
        $request = new Request([], [], ['_sylius' => ['state_machine' => ['transition' => 'test_transition']]]);
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $stateMachineFactory->get($order, Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_nothing_when_there_is_no_state_machine_transition_request_attribute(
        RequestEvent $event,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
    ): void {
        $request = new Request([], [], ['_sylius' => ['state_machine' => ['graph' => 'test_graph']]]);
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $stateMachineFactory->get($order, Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_does_nothing_when_the_requested_transition_can_be_applied(
        RequestEvent $event,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
    ): void {
        $request = new Request([], [], [
            '_sylius' => ['state_machine' => ['graph' => 'test_graph', 'transition' => 'test_transition']],
        ]);
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $stateMachineFactory->get($order, 'test_graph')->willReturn($stateMachine);
        $stateMachine->can('test_transition')->willReturn(true);
        $event->setResponse(Argument::any())->shouldNotBeCalled();

        $this->onKernelRequest($event);
    }

    function it_redirects_when_the_requested_transition_cannot_be_applied(
        RequestEvent $event,
        RequestMatcherInterface $requestMatcher,
        CartContextInterface $cartContext,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        CheckoutStateUrlGeneratorInterface $urlGenerator,
    ): void {
        $request = new Request([], [], [
            '_sylius' => ['state_machine' => ['graph' => 'test_graph', 'transition' => 'test_transition']],
        ]);
        $event->isMainRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $stateMachineFactory->get($order, 'test_graph')->willReturn($stateMachine);
        $stateMachine->can('test_transition')->willReturn(false);
        $urlGenerator->generateForOrderCheckoutState($order)->willReturn('/target-url');
        $event->setResponse(Argument::type(RedirectResponse::class))->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
