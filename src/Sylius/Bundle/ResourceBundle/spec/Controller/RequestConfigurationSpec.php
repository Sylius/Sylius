<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\ODM\PHPCR\Document\Resource;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Sylius\Component\Resource\Metadata\ResourceMetadataInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin \Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
class ConfigurationSpec extends ObjectBehavior
{
    function let(ResourceMetadataInterface $metadata, Request $request, Parameters $parameters)
    {
        $this->beConstructedWith($metadata, $request, $parameters);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration');
    }

    function it_has_request(Request $request)
    {
        $this->getRequest()->shouldReturn($request);
    }
    
    function it_checks_if_its_a_html_request(Request $request)
    {
        $request->getRequestFormat()->shouldBeCalled()->willReturn('html');
        $this->isHtmlRequest()->shouldReturn(true);

        $request->getRequestFormat()->shouldBeCalled()->willReturn('json');
        $this->isHtmlRequest()->shouldReturn(false);
    }

    function it_returns_default_template_names(ResourceMetadataInterface $metadata)
    {
        $metadata->getParameter('templates')->shouldBeCalled()->willReturn('SyliusAdminBundle:Product');

        $this->getDefaultTemplate('index.html')->shouldReturn('SyliusAdminBundle:Product:index.html.twig');
        $this->getDefaultTemplate('show.html')->shouldReturn('SyliusAdminBundle:Product:show.html.twig');
        $this->getDefaultTemplate('create.html')->shouldReturn('SyliusAdminBundle:Product:create.html.twig');
        $this->getDefaultTemplate('update.html')->shouldReturn('SyliusAdminBundle:Product:update.html.twig');
        $this->getDefaultTemplate('custom.html')->shouldReturn('SyliusAdminBundle:Product:custom.html.twig');
    }

    function it_takes_the_custom_template_if_specified(ResourceMetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getParameter('templates')->shouldBeCalled()->willReturn('SyliusAdminBundle:Product');
        $parameters->get('template', 'SyliusAdminBundle:Product:show.html.twig')->shouldBeCalled()->willReturn('AppBundle:Product:show.html.twig');

        $this->getTemplate('foo.html.twig')->shouldReturn('AppBundle:Product:show.html.twig');
    }

    function it_generates_form_type(ResourceMetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->shouldBeCalled()->willReturn('sylius');
        $metadata->getResourceName()->shouldBeCalled()->willReturn('product');

        $parameters->get('form', 'sylius_product')->willReturn('sylius_product');
        $this->getFormType()->shouldReturn('sylius_product');

        $parameters->get('form', 'sylius_product')->willReturn('sylius_product_pricing');
        $this->getFormType()->shouldReturn('sylius_product_pricing');
    }

    function it_generates_route_names(ResourceMetadataInterface $metadata)
    {
        $metadata->getApplicationName()->shouldBeCalled()->willReturn('sylius');
        $metadata->getResourceName()->shouldBeCalled()->willReturn('product');

        $this->getRouteName('index')->shouldReturn('sylius_product_index');
        $this->getRouteName('show')->shouldReturn('sylius_product_show');
        $this->getRouteName('custom')->shouldReturn('sylius_product_custom');
    }

    function it_generates_redirect_referer(Parameters $parameters, Request $request, ParameterBag $bag)
    {
        $request->headers = $bag;
        $bag->get('referer')->willReturn('http://myurl.com');
        $parameters->get('redirect')->willReturn(array('referer' => 'http://myurl.com'));
        $this->getRedirectReferer()->shouldReturn('http://myurl.com');
    }

    function it_generates_redirect_route(Parameters $parameters)
    {
        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectRoute('index')->shouldReturn('sylius_product_index');

        $parameters->get('redirect')->willReturn(array('route' => 'myRoute'));
        $this->getRedirectRoute('show')->shouldReturn('myRoute');

        $parameters->get('redirect')->willReturn('myRoute');
        $this->getRedirectRoute('custom')->shouldReturn('myRoute');
    }

    function it_returns_array_as_redirect_parameters(Parameters $parameters)
    {
        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectParameters()->shouldReturn(array());

        $parameters->get('redirect')->willReturn('string');
        $this->getRedirectParameters()->shouldReturn(array());

        $parameters->get('redirect')->willReturn(array('parameters' => array('myParameter')));
        $this->getRedirectParameters()->shouldReturn(array('myParameter'));

        $params = array('myParameter');
        $parameters->get('redirect')->willReturn(array('parameters' => array('myParameter')));

        $this->getRedirectParameters('resource')->shouldReturn($params);
    }

    function it_checks_limit_is_enable(Parameters $parameters)
    {
        $parameters->get('limit', Argument::any())->willReturn(10);
        $this->isLimited()->shouldReturn(true);

        $parameters->get('limit', Argument::any())->willReturn(null);
        $this->isLimited()->shouldReturn(false);
    }

    function it_has_limit_parameter(Parameters $parameters)
    {
        $parameters->get('limit', false)->willReturn(true);
        $parameters->get('limit', 10)->willReturn(10);
        $this->getLimit()->shouldReturn(10);

        $parameters->get('limit', false)->willReturn(false);
        $parameters->get('limit', 10)->willReturn(null);
        $this->getLimit()->shouldReturn(null);
    }

    function it_checks_paginate_is_enable(Parameters $parameters)
    {
        $parameters->get('paginate', Argument::any())->willReturn(10);
        $this->isPaginated()->shouldReturn(true);

        $parameters->get('paginate', Argument::any())->willReturn(null);
        $this->isPaginated()->shouldReturn(false);
    }

    function it_has_paginate_parameter(Parameters $parameters)
    {
        $parameters->get('paginate', 10)->willReturn(20);
        $this->getPaginationMaxPerPage()->shouldReturn(20);

        $parameters->get('paginate', 10)->willReturn(10);
        $this->getPaginationMaxPerPage()->shouldReturn(10);
    }

    function it_checks_if_the_resource_is_filterable(Parameters $parameters)
    {
        $parameters->get('filterable', Argument::any())->willReturn(true);
        $this->isFilterable()->shouldReturn(true);

        $parameters->get('filterable', Argument::any())->willReturn(null);
        $this->isFilterable()->shouldReturn(false);
    }

    function it_has_no_filterable_parameter(Parameters $parameters)
    {
        $defaultCriteria = array('property' => 'myValue');

        $parameters->get('criteria', Argument::any())->willReturn(array());
        $parameters->get('filterable', false)->willReturn(false);

        $this->getCriteria($defaultCriteria)->shouldBeArray();
        $this->getCriteria($defaultCriteria)->shouldHaveCount(1);
    }

    function it_has_criteria_parameter(Parameters $parameters, Request $request)
    {
        $criteria = array('property' => 'myNewValue');

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', Argument::any())->willReturn(array());
        $request->get('criteria', array())->willReturn($criteria);
        $this->getCriteria()->shouldReturn($criteria);
    }

    function it_allows_to_override_criteria_parameter_in_route(Parameters $parameters, Request $request)
    {
        $criteria = array('property' => 'myValue');
        $overriddenCriteria = array('other_property' => 'myNewValue');
        $combinedCriteria = array('property' => 'myValue', 'other_property' => 'myNewValue');

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', array())->willReturn($criteria);
        $request->get('criteria', array())->willReturn($overriddenCriteria);

        $this->getCriteria()->shouldReturn($combinedCriteria);

        $defaultCriteria = array('slug' => 'foo');
        $combinedDefaultCriteria = array('property' => 'myValue', 'slug' => 'foo', 'other_property' => 'myNewValue');

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', Argument::any())->willReturn($criteria);
        $request->get('criteria', array())->willReturn($overriddenCriteria);

        $this->getCriteria($defaultCriteria)->shouldReturn($combinedDefaultCriteria);

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', array())->willReturn(array('filter' => 'route'));
        $request->get('criteria', array())->willReturn(array('filter' => 'request'));

        $this->getCriteria(array('filter' => 'default'))->shouldReturn(array('filter' => 'request'));
    }

    function it_checks_if_the_resource_is_sortable(Parameters $parameters)
    {
        $parameters->get('sortable', Argument::any())->willReturn(true);
        $this->isSortable()->shouldReturn(true);

        $parameters->get('sortable', Argument::any())->willReturn(null);
        $this->isSortable()->shouldReturn(false);
    }

    function it_has_sorting_parameter(Parameters $parameters, Request $request)
    {
        $sorting = array('property' => 'asc');

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', Argument::any())->willReturn($sorting);
        $request->get('sorting', array())->willReturn($sorting);
        $this->getSorting()->shouldReturn($sorting);
    }

    function it_has_no_sortable_parameter(Parameters $parameters)
    {
        $defaultSorting = array('property' => 'desc');

        $parameters->get('sorting', Argument::any())->willReturn(array());
        $parameters->get('sortable', false)->willReturn(false);

        $this->getSorting($defaultSorting)->shouldBeArray();
        $this->getSorting($defaultSorting)->shouldHaveCount(1);
    }

    function it_allows_to_override_sorting_parameter_in_route(Parameters $parameters, Request $request)
    {
        $sorting = array('property' => 'desc');
        $overriddenSorting = array('other_property' => 'asc');
        $combinedSorting = array('other_property' => 'asc', 'property' => 'desc');

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', array())->willReturn($sorting);
        $request->get('sorting', array())->willReturn($overriddenSorting);

        $this->getSorting()->shouldReturn($combinedSorting);

        $defaultSorting = array('foo' => 'bar');
        $combinedDefaultSorting = array('other_property' => 'asc', 'property' => 'desc', 'foo' => 'bar');

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', Argument::any())->willReturn($sorting);
        $request->get('sorting', array())->willReturn($overriddenSorting);

        $this->getSorting($defaultSorting)->shouldReturn($combinedDefaultSorting);

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', array())->willReturn(array('sort' => 'route'));
        $request->get('sorting', array())->willReturn(array('sort' => 'request'));

        $this->getSorting(array('sort' => 'default'))->shouldReturn(array('sort' => 'request'));
    }

    function it_has_repository_method_parameter(Parameters $parameters)
    {
        $parameters->get('repository', array('method' => 'myDefaultMethod'))
            ->willReturn(array('method' => 'myDefaultMethod'));
        $this->getRepositoryMethod('myDefaultMethod')->shouldReturn('myDefaultMethod');

        $parameters->get('repository', array('method' => 'myDefaultMethod'))
            ->willReturn('myMethod');
        $this->getRepositoryMethod('myDefaultMethod')->shouldReturn('myMethod');
    }

    function it_has_repository_arguments_parameter(Parameters $parameters)
    {
        $defaultArguments = array('arguments' => 'value');
        $parameters->get('repository', array())->willReturn($defaultArguments);
        $this->getRepositoryArguments($defaultArguments)->shouldReturn('value');

        $arguments = array('arguments' => 'myValue');
        $parameters->get('repository', array())->willReturn($arguments);
        $this->getRepositoryArguments($defaultArguments)->shouldReturn('myValue');
    }

    function it_has_factory_method_parameter(Parameters $parameters)
    {
        $parameters->get('factory', array('method' => 'myDefaultMethod'))
            ->willReturn(array('method' => 'myDefaultMethod'));
        $this->getFactoryMethod('myDefaultMethod')->shouldReturn('myDefaultMethod');

        $parameters->get('factory', array('method' => 'myDefaultMethod'))
            ->willReturn('myMethod');
        $this->getFactoryMethod('myDefaultMethod')->shouldReturn('myMethod');
    }

    function it_has_factory_arguments_parameter(Parameters $parameters)
    {
        $defaultArguments = array('arguments' => 'value');
        $parameters->get('factory', array())->willReturn($defaultArguments);
        $this->getFactoryArguments($defaultArguments)->shouldReturn('value');

        $arguments = array('arguments' => 'myValue');
        $parameters->get('factory', array())->willReturn($arguments);
        $this->getFactoryArguments($defaultArguments)->shouldReturn('myValue');
    }

    function it_has_flash_message_parameter(Parameters $parameters)
    {
        $parameters->get('flash', 'sylius.product.message')->willReturn('sylius.product.message');
        $this->getFlashMessage('message')->shouldReturn('sylius.product.message');

        $parameters->get('flash', 'sylius.product.flash')->willReturn('sylius.product.myMessage');
        $this->getFlashMessage('flash')->shouldReturn('sylius.product.myMessage');
    }

    function it_has_sortable_position_parameter(Parameters $parameters)
    {
        $parameters->get('sortable_position', 'position')->willReturn('position');
        $this->getSortablePosition()->shouldReturn('position');

        $parameters->get('sortable_position', 'position')->willReturn('myPosition');
        $this->getSortablePosition()->shouldReturn('myPosition');
    }
}
