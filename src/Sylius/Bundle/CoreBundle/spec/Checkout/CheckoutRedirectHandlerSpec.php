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
use Sylius\Bundle\CoreBundle\Checkout\CheckoutRedirectHandler;
use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class CheckoutRedirectHandlerSpec extends ObjectBehavior
{
    function let(
        RedirectHandlerInterface $decoratedRedirectHandler,
        CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        RequestMatcherInterface $requestMatcher
    ) {
        $this->beConstructedWith($decoratedRedirectHandler, $checkoutStateUrlGenerator, $requestMatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CheckoutRedirectHandler::class);
    }

    function it_implements_redirect_handler_interface()
    {
        $this->shouldImplement(RedirectHandlerInterface::class);
    }

    function it_delegates_redirection_if_request_is_not_checkout(
        OrderInterface $order,
        RedirectHandlerInterface $decoratedRedirectHandler,
        RedirectResponse $redirectResponse,
        Request $request,
        RequestConfiguration $configuration,
        RequestMatcherInterface $requestMatcher
    ) {
        $configuration->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(false);

        $decoratedRedirectHandler->redirectToResource($configuration, $order)->willReturn($redirectResponse);

        $this->redirectToResource($configuration, $order)->shouldReturn($redirectResponse);
    }

    function it_delegates_redirection_if_request_has_redirection_configured(
        OrderInterface $order,
        RedirectHandlerInterface $decoratedRedirectHandler,
        RedirectResponse $redirectResponse,
        Request $request,
        RequestConfiguration $configuration,
        RequestMatcherInterface $requestMatcher
    ) {
        $configuration->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);

        $request->attributes = new ParameterBag(['_sylius' => ['redirect' => 'sylius_shop_checkout_select_payment']]);

        $decoratedRedirectHandler->redirectToResource($configuration, $order)->willReturn($redirectResponse);

        $this->redirectToResource($configuration, $order)->shouldReturn($redirectResponse);
    }

    function it_redirects_checkout_request_based_on_order_checkout_state(
        CheckoutStateUrlGeneratorInterface $checkoutStateUrlGenerator,
        OrderInterface $order,
        Request $request,
        RequestConfiguration $configuration,
        RequestMatcherInterface $requestMatcher
    ) {
        $configuration->getRequest()->willReturn($request);
        $requestMatcher->matches($request)->willReturn(true);

        $request->attributes = new ParameterBag(['_sylius' => []]);

        $checkoutStateUrlGenerator->generateForOrderCheckoutState($order)->willReturn('http://path-to-redirect');

        $this->redirectToResource($configuration, $order)->shouldBeSameAs(new RedirectResponse('http://path-to-redirect'));
    }

    function it_throws_exception_if_resource_is_not_an_order(
        Request $request,
        RequestConfiguration $configuration,
        RequestMatcherInterface $requestMatcher,
        ResourceInterface $resource
    ) {
        $configuration->getRequest()->willReturn($request);
        $request->attributes = new ParameterBag([]);

        $requestMatcher->matches($request)->willReturn(true);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('redirectToResource', [$configuration, $resource])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchers()
    {
        return [
            'beSameAs' => function ($subject, $key) {
                if (!$subject instanceof RedirectResponse || !$key instanceof RedirectResponse) {
                    return false;
                }

                return $subject->getTargetUrl() === $key->getTargetUrl();
            },
        ];
    }
}
