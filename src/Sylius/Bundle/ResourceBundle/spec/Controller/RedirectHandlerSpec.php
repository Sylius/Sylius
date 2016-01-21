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
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandler;
use Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * @mixin RedirectHandler
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RedirectHandlerSpec extends ObjectBehavior
{
    function let(RouterInterface $router, RouteCollection $routes)
    {
        $router->getRouteCollection()->willReturn($routes);

        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RedirectHandler');
    }
    
    function it_implements_redirect_handler_interface()
    {
        $this->shouldImplement(RedirectHandlerInterface::class);
    }

    function it_redirects_to_resource(
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        RouterInterface $router,
        RouteCollection $routes,
        Route $route
    )
    {
        $configuration->getRedirectParameters($resource)->willReturn(array());
        $configuration->getRedirectRoute('show')->willReturn('my_route');

        $routes->get('my_route')->willReturn($route);

        $router->generate('my_route', array())->shouldBeCalled()->willReturn('http://test.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToResource($configuration, $resource)->shouldHaveType(RedirectResponse::class);
    }

    function it_fallbacks_to_index_route_if_show_does_not_exist(
        RequestConfiguration $configuration,
        ResourceInterface $resource,
        RouterInterface $router,
        RouteCollection $routes
    )
    {
        $configuration->getRedirectParameters($resource)->willReturn(array());
        $configuration->getRedirectRoute('show')->willReturn('app_product_show');
        $configuration->getRedirectRoute('index')->willReturn('app_product_index');

        $routes->get('app_product_show')->willReturn(null);

        $router->generate('app_product_index', array())->shouldBeCalled()->willReturn('http://test.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToResource($configuration, $resource)->shouldHaveType(RedirectResponse::class);
    }

    function it_redirects_to_index(RequestConfiguration $configuration, $router)
    {
        $configuration->getRedirectRoute('index')->willReturn('my_route');
        $configuration->getRedirectParameters()->willReturn(array());

        $router->generate('my_route', array())->willReturn('http://myurl.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToIndex($configuration)->shouldHaveType(RedirectResponse::class);
    }

    function it_redirects_to_route(RequestConfiguration $configuration, $router)
    {
        $router->generate('route', array('parameter' => 'value'))->willReturn('http://myurl.com');

        $this
            ->redirectToRoute($configuration, 'route', array('parameter' => 'value'))
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

        $configuration->getRequest()->willreturn($request);
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->getRedirectReferer()->willreturn('http://myurl.com');
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
