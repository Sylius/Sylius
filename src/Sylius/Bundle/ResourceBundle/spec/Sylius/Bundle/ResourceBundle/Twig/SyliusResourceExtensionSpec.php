<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ResourceBundle\Twig;

use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\Routing\RouterInterface;

/**
 * Sylius resource extension for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class SyliusResourceExtensionSpec extends ObjectBehavior
{
    function let(ContainerInterface $container, RouterInterface $router, TwigEngine $templating)
    {
        $container->get('router')->willReturn($router);
        $container->get('templating')->willReturn($templating);

        $this->beConstructedWith(
            $container,
            'SyliusResourceBundle:Twig:paginate.html.twig',
            'SyliusResourceBundle:Twig:sorting.html.twig'
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Twig\SyliusResourceExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_should_fetch_request(GetResponseEvent $event)
    {
        $this->fetchRequest($event);
    }

    function it_should_define_twig_function()
    {
        $this->getFunctions()->shouldBeArray();
        $this->getFunctions()->shouldHaveCount(2);
    }

    function it_should_render_a_sorting_link(
        Request $request,
        GetResponseEvent $event,
        RouterInterface $router,
        TwigEngine $templating
    ) {
        $request->get('sorting')->willReturn(array());

        $event = $this->getGetResponseEvent($request, $event);

        $router->generate(
            'route_name',
            array('sorting' => array('propertyName' => 'asc'))
        )->shouldBeCalled()->willReturn('?sorting[propertyName]=asc');

        $templating->render(
            'SyliusResourceBundle:Twig:sorting.html.twig',
            array(
                'url' => '?sorting[propertyName]=asc',
                'label' => 'fieldName',
                'icon' => false,
                'currentOrder' => null,
            )
        )->shouldBeCalled();

        $this->fetchRequest($event);
        $this->renderSortingLink('propertyName', 'fieldName');
    }

    function it_should_render_a_sorting_desc_link(
        Request $request,
        GetResponseEvent $event,
        RouterInterface $router,
        TwigEngine $templating
    ) {
        $request->get('sorting')->willReturn(array('propertyName' => 'asc'));

        $event = $this->getGetResponseEvent($request, $event);

        $router->generate(
            'route_name',
            array('sorting' => array('propertyName' => 'desc'))
        )->shouldBeCalled()->willReturn('?sorting[propertyName]=desc');

        $templating->render(
            'SyliusResourceBundle:Twig:sorting.html.twig',
            array(
                'url' => '?sorting[propertyName]=desc',
                'label' => 'fieldName',
                'icon' => true,
                'currentOrder' => 'asc',
            )
        )->shouldBeCalled();

        $this->fetchRequest($event);
        $this->renderSortingLink('propertyName', 'fieldName');
    }

    function it_should_render_a_sorting_asc_link(
        Request $request,
        GetResponseEvent $event,
        RouterInterface $router,
        TwigEngine $templating
    ) {
        $request->get('sorting')->willReturn(array());

        $event = $this->getGetResponseEvent($request, $event);

        $router->generate(
            'route_name',
            array('sorting' => array('otherName' => 'asc'))
        )->shouldBeCalled()->willReturn('?sorting[otherName]=asc');

        $templating->render(
            'SyliusResourceBundle:Twig:sorting.html.twig',
            array(
                'url' => '?sorting[otherName]=asc',
                'label' => 'fieldName',
                'icon' => false,
                'currentOrder' => null,
            )
        )->shouldBeCalled();

        $this->fetchRequest($event);
        $this->renderSortingLink('otherName', 'fieldName');
    }

    function it_should_render_a_sorting_link_with_custom_options(
        Request $request,
        GetResponseEvent $event,
        RouterInterface $router,
        TwigEngine $templating
    ) {
        $request->get('sorting')->willReturn(array('propertyName' => 'asc'));

        $event = $this->getGetResponseEvent($request, $event);

        $router->generate(
            'new_route',
            array(
                'sorting' => array('propertyName' => 'desc'),
                'params' => 'value',
            )
        )->shouldBeCalled()->willReturn('?sorting[propertyName]=asc&params=value');

        $templating->render(
            'SyliusResourceBundle:Twig:newsorting.html.twig',
            array(
                'url' => '?sorting[propertyName]=asc&params=value',
                'label' => 'fieldName',
                'icon' => true,
                'currentOrder' => 'asc',
            )
        )->shouldBeCalled();

        $this->fetchRequest($event);
        $this->renderSortingLink('propertyName', 'fieldName', null, array(
            'route' => 'new_route',
            'template' => 'SyliusResourceBundle:Twig:newsorting.html.twig',
            'route_params' => array('params' => 'value'),
        ));
    }

    function it_should_not_render_sorting_link(Request $request, GetResponseEvent $event)
    {
        $request->get('sorting')->willReturn(array());

        $event = $this->getGetResponseEvent(
            $request,
            $event,
            'route_name',
            array('_sylius' => array('sortable' => false))
        );

        $this->fetchRequest($event);
        $this->renderSortingLink('propertyName', 'fieldName')->shouldReturn('fieldName');
    }

    function it_should_render_a_paginate_select(
        Request $request,
        GetResponseEvent $event,
        Pagerfanta $paginator,
        RouterInterface $router,
        TwigEngine $templating
    ) {
        $limits = array(10, 20);

        $event = $this->getGetResponseEvent(
            $request,
            $event,
            'route_name',
            array(
                'page' => 3,
                '_sylius' => array('paginate' => '$paginate'),
            )
        );

        foreach ($limits as $limit) {
            $router->generate(
                'route_name',
                array(
                    'page' => 1,
                    'paginate' => $limit,
                    '_sylius' => array('paginate' => '$paginate'),
                )
            )->shouldBeCalled()->willReturn('?paginate=' . $limit);
        }

        $templating->render(
            'SyliusResourceBundle:Twig:paginate.html.twig',
            array(
                'paginator' => $paginator,
                'limits' => array(
                    10 => '?paginate=10',
                    20 => '?paginate=20',
                ),
            )
        )->shouldBeCalled();

        $this->fetchRequest($event);
        $this->renderPaginateSelect($paginator, array(10,20));
    }

    function it_should_render_a_paginate_select_with_custom_options(
        Request $request,
        GetResponseEvent $event,
        Pagerfanta $paginator,
        RouterInterface $router,
        TwigEngine $templating
    ) {
        $limits = array(10, 20);

        $event = $this->getGetResponseEvent(
            $request,
            $event,
            'route_name',
            array(
                'page' => 3,
                '_sylius' => array('paginate' => '$paginate'),
            )
        );

        foreach ($limits as $limit) {
            $router->generate(
                'new_route',
                array(
                    'page' => 1,
                    'params' => 'value',
                    'paginate' => $limit,
                    '_sylius' => array('paginate' => '$paginate'),
                )
            )->shouldBeCalled()->willReturn('?paginate=' . $limit . '&params=value');
        }

        $templating->render(
            'SyliusResourceBundle:Twig:newpaginate.html.twig',
            array(
                'paginator' => $paginator,
                'limits' => array(
                    10 => '?paginate=10&params=value',
                    20 => '?paginate=20&params=value',
                ),
            )
        )->shouldBeCalled();

        $this->fetchRequest($event);
        $this->renderPaginateSelect($paginator, array(10,20), array(
            'route' => 'new_route',
            'template' => 'SyliusResourceBundle:Twig:newpaginate.html.twig',
            'route_params' => array('params' => 'value'),
        ));
    }

    function it_should_have_a_name()
    {
        $this->getName()->shouldReturn('sylius_resource');
    }

    private function getGetResponseEvent(
        Request $request,
        GetResponseEvent $event,
        $routeName = 'route_name',
        $routerParams = array()
    ) {
        $request->attributes = new ParameterBag();
        $request->query = new ParameterBag();
        $request->attributes->set('_route', $routeName);
        $request->attributes->set('_route_params', $routerParams);

        $event->getRequestType()->willReturn(HttpKernel::MASTER_REQUEST);
        $event->getRequest()->willReturn($request);

        return $event;
    }
}
