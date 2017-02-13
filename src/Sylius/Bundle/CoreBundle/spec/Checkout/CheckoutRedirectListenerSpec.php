<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutRedirectListener;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CheckoutRedirectListenerSpec extends ObjectBehavior
{
    function let(
        RequestStack $requestStack,
        CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        RequestMatcherInterface $requestMatcher
    ) {
        $this->beConstructedWith($requestStack, $checkoutStateUrlGenerator, $requestMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutRedirectListener::class);
    }

    function it_redirects_to_proper_route_based_on_order_checkout_state(
        CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        OrderInterface $order,
        Request $request,
        RequestMatcherInterface $requestMatcher,
        RequestStack $requestStack,
        ResourceControllerEvent $resourceControllerEvent
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $request->attributes = new ParameterBag(['_sylius' => []]);

        $resourceControllerEvent->getSubject()->willReturn($order);

        $checkoutStateUrlGenerator->generateForOrderCheckoutState($order)->willReturn('http://redirect-path');
        $resourceControllerEvent->setResponse(Argument::type(RedirectResponse::class))->shouldBeCalled();

        $this->handleCheckoutRedirect($resourceControllerEvent);
    }

    function it_does_nothing_if_current_request_is_not_checkout_request(
        Request $request,
        RequestMatcherInterface $requestMatcher,
        RequestStack $requestStack,
        ResourceControllerEvent $resourceControllerEvent
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(false);

        $resourceControllerEvent->getSubject()->shouldNotBeCalled();

        $this->handleCheckoutRedirect($resourceControllerEvent);
    }

    function it_does_nothing_if_current_request_has_redirect_configured(
        Request $request,
        RequestMatcherInterface $requestMatcher,
        RequestStack $requestStack,
        ResourceControllerEvent $resourceControllerEvent
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $request->attributes = new ParameterBag(['_sylius' => ['redirect' => 'redirect_route']]);

        $resourceControllerEvent->getSubject()->shouldNotBeCalled();

        $this->handleCheckoutRedirect($resourceControllerEvent);
    }

    function it_throws_exception_if_event_subject_is_not_an_order(
        Request $request,
        RequestMatcherInterface $requestMatcher,
        RequestStack $requestStack,
        ResourceControllerEvent $resourceControllerEvent
    ) {
        $requestStack->getCurrentRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);
        $request->attributes = new ParameterBag(['_sylius' => []]);

        $resourceControllerEvent->getSubject()->willReturn('bad-object');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('handleCheckoutRedirect', [$resourceControllerEvent])
        ;
    }
}
