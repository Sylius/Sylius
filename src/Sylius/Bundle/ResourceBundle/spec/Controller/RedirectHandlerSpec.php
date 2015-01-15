<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Configuration;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class RedirectHandlerSpec extends ObjectBehavior
{
    function let(Configuration $config, RouterInterface $router)
    {
        $this->beConstructedWith($config, $router);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RedirectHandler');
    }

    function it_redirects_to_resource($config, $router)
    {
        $config->getRedirectParameters('resource')->willReturn(array());
        $config->getRedirectRoute('show')->willReturn('my_route');
        $router->generate('my_route', array())->willReturn('http://myurl.com');
        $config->getRedirectHash()->willReturn(null);

        $this->redirectTo('resource')->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirecst_to_index($config, $router)
    {
        $config->getRedirectRoute('index')->willReturn('my_route');
        $config->getRedirectParameters()->willReturn(array());
        $router->generate('my_route', array())->willReturn('http://myurl.com');
        $config->getRedirectHash()->willReturn(null);

        $this->redirectToIndex()->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirects_to_route($router)
    {
        $router->generate('my_route', array('my_parameter' => 'value'))->willReturn('http://myurl.com');

        $this->redirectToRoute('my_route', array('my_parameter' => 'value'))
            ->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirects($config)
    {
        $config->getRedirectHash()->willReturn(null);
        $this->redirect('http://myurl.com')->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }

    function it_redirect_to_referer($config, Request $request, ParameterBag $bag)
    {
        $request->headers = $bag;

        $bag->get('referer')->willReturn('http://myurl.com');
        $config->getRequest()->willreturn($request);
        $config->getRedirectHash()->willReturn(null);
        $config->getRedirectReferer()->willreturn('http://myurl.com');

        $this->redirectToReferer()->shouldHaveType('Symfony\Component\HttpFoundation\RedirectResponse');
    }
}
