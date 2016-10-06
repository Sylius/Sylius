<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandler;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * @mixin RedirectHandler
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class RedirectHandlerSpec extends ObjectBehavior
{
    function let(RouterInterface $router)
    {
        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RedirectHandler::class);
    }

    function it_implements_redirect_handler_interface()
    {
        $this->shouldImplement(RedirectHandlerInterface::class);
    }

    function it_redirects_to_resource(
        RouterInterface $router,
        RequestConfiguration $configuration,
        ResourceInterface $resource
    ) {
        $configuration->getRedirectParameters($resource)->willReturn([]);
        $configuration->getRedirectRoute('show')->willReturn('my_route');

        $router->generate('my_route', [])->shouldBeCalled()->willReturn('http://test.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToResource($configuration, $resource)->shouldHaveType(RedirectResponse::class);
    }

    function it_fallbacks_to_index_route_if_show_does_not_exist(
        RouterInterface $router,
        RequestConfiguration $configuration,
        ResourceInterface $resource
    ) {
        $configuration->getRedirectParameters($resource)->willReturn([]);
        $configuration->getRedirectRoute('show')->willReturn('app_resource_show');
        $configuration->getRedirectRoute('index')->willReturn('app_resource_index');

        $router->generate('app_resource_show', [])->shouldBeCalled()->willThrow(RouteNotFoundException::class);
        $router->generate('app_resource_index', [])->shouldBeCalled()->willReturn('http://test.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToResource($configuration, $resource)->shouldHaveType(RedirectResponse::class);
    }

    function it_redirects_to_index(
        RouterInterface $router,
        RequestConfiguration $configuration,
        ResourceInterface $resource
    ) {
        $configuration->getRedirectRoute('index')->willReturn('my_route');
        $configuration->getRedirectParameters($resource)->willReturn([]);

        $router->generate('my_route', [])->willReturn('http://myurl.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToIndex($configuration, $resource)->shouldHaveType(RedirectResponse::class);
    }

    function it_redirects_to_route(RouterInterface $router, RequestConfiguration $configuration)
    {
        $router->generate('route', ['parameter' => 'value'])->willReturn('http://myurl.com');

        $this
            ->redirectToRoute($configuration, 'route', ['parameter' => 'value'])
            ->shouldHaveType(RedirectResponse::class)
        ;
    }

    function it_redirects(RequestConfiguration $configuration)
    {
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirect($configuration, 'http://myurl.com')->shouldHaveType(RedirectResponse::class);
    }

    function it_redirect_to_referer(RequestConfiguration $configuration, Request $request, ParameterBag $bag)
    {
        $request->headers = $bag;

        $bag->get('referer')->willReturn('http://myurl.com');

        $configuration->getRequest()->willReturn($request);
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->getRedirectReferer()->willReturn('http://myurl.com');
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToReferer($configuration)->shouldHaveType(RedirectResponse::class);
    }

    function it_redirects_with_header(RequestConfiguration $configuration)
    {
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(true);

        $this->redirect($configuration, 'http://myurl.com')->shouldHaveType(Response::class);
    }
}
