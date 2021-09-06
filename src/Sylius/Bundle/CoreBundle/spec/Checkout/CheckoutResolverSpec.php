<?php

namespace spec\Sylius\Bundle\CoreBundle\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class CheckoutResolverSpec extends ObjectBehavior
{
    function let(
        CartContextInterface $cartContext,
        CheckoutStateUrlGeneratorInterface $urlGenerator,
        RequestMatcherInterface $requestMatcher,
        FactoryInterface $stateMachineFactory,
        RequestEvent $event,
        Request $request,
        OrderInterface $order,
        StateMachineInterface $stateMachine
    ): void {
        $event->isMasterRequest()->willReturn(true);
        $event->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $urlGenerator->generateForCart()->willReturn('http://domain.tld/cart/url');
        $cartContext->getCart()->willReturn($order);
        $order->isEmpty()->willReturn(false);
        $request->attributes = new ParameterBag(
            ['_sylius' => ['state_machine' => ['graph' => 'requested_graph', 'transition' => 'requested_transition']]]
        );
        $stateMachineFactory->get($order, 'requested_graph')->willReturn($stateMachine);
        $stateMachine->can('requested_transition')->willReturn(false);
        $urlGenerator->generateForOrderCheckoutState($order)->willReturn('http://domain.tld/order/checkout/state/url');

        $this->beConstructedWith($cartContext, $urlGenerator, $requestMatcher, $stateMachineFactory);
    }

    function it_does_not_set_response_if_it_is_not_master_request(RequestEvent $event): void
    {
        $event->isMasterRequest()->willReturn(false);

        $this->onKernelRequest($event);

        $event->setResponse(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_does_not_set_response_if_request_does_not_match(
        RequestEvent $event,
        Request $request,
        RequestMatcherInterface $requestMatcher
    ): void {
        $requestMatcher->matches($request)->willReturn(false);

        $this->onKernelRequest($event);

        $event->setResponse(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_sets_cart_url_redirect_response_if_cart_is_empty(RequestEvent $event, OrderInterface $order): void
    {
        $order->isEmpty()->willReturn(true);

        $this->onKernelRequest($event);

        $event->setResponse(new RedirectResponse('http://domain.tld/cart/url'))->shouldHaveBeenCalled();
    }

    function it_sets_cart_url_redirect_response_if_request_has_not_sylius_attributes(RequestEvent $event, Request $request): void
    {
        $request->attributes = new ParameterBag();

        $this->onKernelRequest($event);

        $event->setResponse(new RedirectResponse('http://domain.tld/cart/url'))->shouldHaveBeenCalled();
    }

    function it_does_not_set_response_if_state_machine_can_perform_requested_transition(
        RequestEvent $event,
        StateMachineInterface $stateMachine
    ): void {
        $stateMachine->can('requested_transition')->willReturn(true);

        $this->onKernelRequest($event);

        $event->setResponse(Argument::any())->shouldNotHaveBeenCalled();
    }

    function it_sets_order_checkout_state_redirect_response(
        RequestEvent $event
    ): void {
        $this->onKernelRequest($event);

        $event->setResponse(new RedirectResponse('http://domain.tld/order/checkout/state/url'))->shouldHaveBeenCalled();
    }
}
