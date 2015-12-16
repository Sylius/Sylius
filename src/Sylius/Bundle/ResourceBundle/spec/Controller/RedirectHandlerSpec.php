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
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RedirectHandlerSpec extends ObjectBehavior
{
    function let(RouterInterface $router)
    {
        $this->beConstructedWith($router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RedirectHandler');
    }
    
    function it_implements_redirect_handler_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ResourceBundle\Controller\RedirectHandlerInterface');
    }

    function it_redirects_to_resource(RequestConfiguration $configuration, ResourceInterface $resource, $router)
    {
        $configuration->getRedirectParameters($resource)->willReturn(array());
        $configuration->getRedirectRoute('show')->willReturn('my_route');

        $router->generate('my_route', array())->willReturn('http://myurl.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToResource($configuration, $resource)->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirects_to_index(RequestConfiguration $configuration, $router)
    {
        $configuration->getRedirectRoute('index')->willReturn('my_route');
        $configuration->getRedirectParameters()->willReturn(array());

        $router->generate('my_route', array())->willReturn('http://myurl.com');

        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToIndex($configuration)->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirects_to_route(RequestConfiguration $configuration, $router)
    {
        $router->generate('route', array('parameter' => 'value'))->willReturn('http://myurl.com');

        $this
            ->redirectToRoute($configuration, 'route', array('parameter' => 'value'))
            ->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse')
        ;
    }

    function it_redirects(RequestConfiguration $configuration)
    {
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirect($configuration, 'http://myurl.com')->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirect_to_referer(RequestConfiguration $configuration, Request $request, ParameterBag $bag)
    {
        $request->headers = $bag;

        $bag->get('referer')->willReturn('http://myurl.com');

        $configuration->getRequest()->willreturn($request);
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->getRedirectReferer()->willreturn('http://myurl.com');
        $configuration->isHeaderRedirection()->willReturn(false);

        $this->redirectToReferer($configuration)->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirects_with_header(RequestConfiguration $configuration)
    {
        $configuration->getRedirectHash()->willReturn(null);
        $configuration->isHeaderRedirection()->willReturn(true);

        $this->redirect($configuration, 'http://myurl.com')->shouldHaveType('Symfony\Component\HttpFoundation\Response');
    }
}
