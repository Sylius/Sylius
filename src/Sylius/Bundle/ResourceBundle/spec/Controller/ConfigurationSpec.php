<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\ParametersParser;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resource controller configuration product.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
class ConfigurationSpec extends ObjectBehavior
{
    public function let(Request $request, Parameters $parameters, ParametersParser $parser)
    {
        $this->beConstructedWith(
            $parser,
            'sylius',
            'product',
            'SyliusWebBundle:Product',
            'twig',
            array(
                'paginate' => false,
                'default_page_size' => 10,
                'limit' => 10,
                'sortable' => false,
                'sorting' => null,
                'filterable' => false,
                'criteria' => null,
            )
        );
        $request->attributes = new ParameterBag();

        $this->setRequest($request);
        $this->setParameters($parameters);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\Configuration');
    }

    public function it_has_parameters()
    {
        $this->getParameters()->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\Parameters');
    }

    public function its_parameter_is_mutable(Parameters $parameters)
    {
        $this->setParameters($parameters);
    }

    public function it_has_request()
    {
        $this->getRequest()->shouldHaveType('Symfony\Component\HttpFoundation\Request');
    }

    public function its_request_is_mutable(Request $request)
    {
        $this->setRequest($request);
    }

    public function it_returns_assigned_bundle_prefix()
    {
        $this->getBundlePrefix()->shouldReturn('sylius');
    }

    public function it_returns_assigned_resource_name()
    {
        $this->getResourceName()->shouldReturn('product');
    }

    public function it_returns_plural_resource_name()
    {
        $this->getPluralResourceName()->shouldReturn('products');
    }

    public function it_returns_assigned_template_namespace()
    {
        $this->getTemplateNamespace()->shouldReturn('SyliusWebBundle:Product');
    }

    public function it_returns_assigned_templating_engine()
    {
        $this->getTemplatingEngine()->shouldReturn('twig');
    }

    public function its_api_request_when_format_is_not_html(Request $request)
    {
        $this->setRequest($request);

        $request->getRequestFormat()->willReturn('html');
        $this->isApiRequest()->shouldReturn(false);

        $request->getRequestFormat()->willReturn('xml');
        $this->isApiRequest()->shouldReturn(true);

        $request->getRequestFormat()->willReturn('json');
        $this->isApiRequest()->shouldReturn(true);
    }

    public function it_generates_service_names()
    {
        $this->getServiceName('manager')->shouldReturn('sylius.manager.product');
        $this->getServiceName('repository')->shouldReturn('sylius.repository.product');
        $this->getServiceName('controller')->shouldReturn('sylius.controller.product');
    }

    public function it_generates_event_names()
    {
        $this->getEventName('create')->shouldReturn('sylius.product.create');
        $this->getEventName('created')->shouldReturn('sylius.product.created');
    }

    public function it_generates_template_names()
    {
        $this->getTemplateName('index.html')->shouldReturn('SyliusWebBundle:Product:index.html.twig');
        $this->getTemplateName('show.html')->shouldReturn('SyliusWebBundle:Product:show.html.twig');
        $this->getTemplateName('create.html')->shouldReturn('SyliusWebBundle:Product:create.html.twig');
        $this->getTemplateName('update.html')->shouldReturn('SyliusWebBundle:Product:update.html.twig');
        $this->getTemplateName('custom.html')->shouldReturn('SyliusWebBundle:Product:custom.html.twig');
    }

    public function it_generates_view_template(Parameters $parameters)
    {
        $parameters->get('template', 'SyliusWebBundle:Product:create.html.twig')
            ->willReturn('SyliusWebBundle:Product:create.html.twig');
        $this->getTemplate('create.html')->shouldReturn('SyliusWebBundle:Product:create.html.twig');

        $parameters->get('template', 'SyliusWebBundle:Product:create.html.twig')
            ->willReturn('MyBundleWebBundle:Product:create.html.twig');
        $this->getTemplate('create.html')->shouldReturn('MyBundleWebBundle:Product:create.html.twig');
    }

    public function it_generates_form_type(Parameters $parameters)
    {
        $parameters->get('form', 'sylius_product')->willReturn('sylius_product');
        $this->getFormType()->shouldReturn('sylius_product');

        $parameters->get('form', 'sylius_product')->willReturn('sylius_variant');
        $this->getFormType()->shouldReturn('sylius_variant');
    }

    public function it_generates_route_names()
    {
        $this->getRouteName('index')->shouldReturn('sylius_product_index');
        $this->getRouteName('show')->shouldReturn('sylius_product_show');
        $this->getRouteName('custom')->shouldReturn('sylius_product_custom');
    }

    public function it_generates_redirect_referer(Parameters $parameters, Request $request, ParameterBag $bag)
    {
        $request->headers = $bag;
        $bag->get('referer')->willReturn('http://myurl.com');
        $parameters->get('redirect')->willReturn(array('referer' => 'http://myurl.com'));
        $this->getRedirectReferer()->shouldReturn('http://myurl.com');
    }

    public function it_generates_redirect_route(Parameters $parameters)
    {
        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectRoute('index')->shouldReturn('sylius_product_index');

        $parameters->get('redirect')->willReturn(array('route' => 'myRoute'));
        $this->getRedirectRoute('show')->shouldReturn('myRoute');

        $parameters->get('redirect')->willReturn('myRoute');
        $this->getRedirectRoute('custom')->shouldReturn('myRoute');
    }

    public function it_returns_array_as_redirect_parameters(Parameters $parameters, ParametersParser $parser)
    {
        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectParameters()->shouldReturn(array());

        $parameters->get('redirect')->willReturn('string');
        $this->getRedirectParameters()->shouldReturn(array());

        $parameters->get('redirect')->willReturn(array('parameters' => array('myParameter')));
        $this->getRedirectParameters()->shouldReturn(array('myParameter'));

        $params = array('myParameter');
        $parameters->get('redirect')->willReturn(array('parameters' => array('myParameter')));
        $parser->process($params, 'resource')->willReturn($params);
        $this->getRedirectParameters('resource')->shouldReturn($params);
    }

    public function it_checks_limit_is_enable(Parameters $parameters)
    {
        $parameters->get('limit', Argument::any())->willReturn(10);
        $this->isLimited()->shouldReturn(true);

        $parameters->get('limit', Argument::any())->willReturn(null);
        $this->isLimited()->shouldReturn(false);
    }

    public function it_has_limit_parameter(Parameters $parameters)
    {
        $parameters->get('limit', false)->willReturn(true);
        $parameters->get('limit', 10)->willReturn(10);
        $this->getLimit()->shouldReturn(10);

        $parameters->get('limit', false)->willReturn(false);
        $parameters->get('limit', 10)->willReturn(null);
        $this->getLimit()->shouldReturn(null);
    }

    public function it_checks_paginate_is_enable(Parameters $parameters)
    {
        $parameters->get('paginate', Argument::any())->willReturn(10);
        $this->isPaginated()->shouldReturn(true);

        $parameters->get('paginate', Argument::any())->willReturn(null);
        $this->isPaginated()->shouldReturn(false);
    }

    public function it_has_paginate_parameter(Parameters $parameters)
    {
        $parameters->get('paginate', 10)->willReturn(20);
        $this->getPaginationMaxPerPage()->shouldReturn(20);

        $parameters->get('paginate', 10)->willReturn(10);
        $this->getPaginationMaxPerPage()->shouldReturn(10);
    }

    public function it_checks_if_the_resource_is_filterable(Parameters $parameters)
    {
        $parameters->get('filterable', Argument::any())->willReturn(true);
        $this->isFilterable()->shouldReturn(true);

        $parameters->get('filterable', Argument::any())->willReturn(null);
        $this->isFilterable()->shouldReturn(false);
    }

    public function it_has_no_filterable_parameter(Parameters $parameters)
    {
        $defaultCriteria = array('property' => 'myValue');

        $parameters->get('criteria', Argument::any())->willReturn(array());
        $parameters->get('filterable', false)->willReturn(false);

        $this->getCriteria($defaultCriteria)->shouldBeArray();
        $this->getCriteria($defaultCriteria)->shouldHaveCount(1);
    }

    public function it_has_criteria_parameter(Parameters $parameters, Request $request)
    {
        $criteria = array('property' => 'myNewValue');

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', Argument::any())->willReturn(array());
        $request->get('criteria', array())->willReturn($criteria);
        $this->getCriteria()->shouldReturn($criteria);
    }

    public function it_allows_to_override_criteria_parameter_in_route(Parameters $parameters, Request $request)
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

    public function it_checks_if_the_resource_is_sortable(Parameters $parameters)
    {
        $parameters->get('sortable', Argument::any())->willReturn(true);
        $this->isSortable()->shouldReturn(true);

        $parameters->get('sortable', Argument::any())->willReturn(null);
        $this->isSortable()->shouldReturn(false);
    }

    public function it_has_sorting_parameter(Parameters $parameters, Request $request)
    {
        $sorting = array('property' => 'asc');

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', Argument::any())->willReturn($sorting);
        $request->get('sorting', array())->willReturn($sorting);
        $this->getSorting()->shouldReturn($sorting);
    }

    public function it_has_no_sortable_parameter(Parameters $parameters)
    {
        $defaultSorting = array('property' => 'desc');

        $parameters->get('sorting', Argument::any())->willReturn(array());
        $parameters->get('sortable', false)->willReturn(false);

        $this->getSorting($defaultSorting)->shouldBeArray();
        $this->getSorting($defaultSorting)->shouldHaveCount(1);
    }

    public function it_allows_to_override_sorting_parameter_in_route(Parameters $parameters, Request $request)
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

    public function it_has_repository_method_parameter(Parameters $parameters)
    {
        $parameters->get('repository', array('method' => 'myDefaultMethod'))
            ->willReturn(array('method' => 'myDefaultMethod'));
        $this->getRepositoryMethod('myDefaultMethod')->shouldReturn('myDefaultMethod');

        $parameters->get('repository', array('method' => 'myDefaultMethod'))
            ->willReturn('myMethod');
        $this->getRepositoryMethod('myDefaultMethod')->shouldReturn('myMethod');
    }

    public function it_has_repository_arguments_parameter(Parameters $parameters)
    {
        $defaultArguments = array('arguments' => 'value');
        $parameters->get('repository', array())->willReturn($defaultArguments);
        $this->getRepositoryArguments($defaultArguments)->shouldReturn('value');

        $arguments = array('arguments' => 'myValue');
        $parameters->get('repository', array())->willReturn($arguments);
        $this->getRepositoryArguments($defaultArguments)->shouldReturn('myValue');
    }

    public function it_has_factory_method_parameter(Parameters $parameters)
    {
        $parameters->get('factory', array('method' => 'myDefaultMethod'))
            ->willReturn(array('method' => 'myDefaultMethod'));
        $this->getFactoryMethod('myDefaultMethod')->shouldReturn('myDefaultMethod');

        $parameters->get('factory', array('method' => 'myDefaultMethod'))
            ->willReturn('myMethod');
        $this->getFactoryMethod('myDefaultMethod')->shouldReturn('myMethod');
    }

    public function it_has_factory_arguments_parameter(Parameters $parameters)
    {
        $defaultArguments = array('arguments' => 'value');
        $parameters->get('factory', array())->willReturn($defaultArguments);
        $this->getFactoryArguments($defaultArguments)->shouldReturn('value');

        $arguments = array('arguments' => 'myValue');
        $parameters->get('factory', array())->willReturn($arguments);
        $this->getFactoryArguments($defaultArguments)->shouldReturn('myValue');
    }

    public function it_has_flash_message_parameter(Parameters $parameters)
    {
        $parameters->get('flash', 'sylius.product.message')->willReturn('sylius.product.message');
        $this->getFlashMessage('message')->shouldReturn('sylius.product.message');

        $parameters->get('flash', 'sylius.product.flash')->willReturn('sylius.product.myMessage');
        $this->getFlashMessage('flash')->shouldReturn('sylius.product.myMessage');
    }

    public function it_has_sortable_position_parameter(Parameters $parameters)
    {
        $parameters->get('sortable_position', 'position')->willReturn('position');
        $this->getSortablePosition()->shouldReturn('position');

        $parameters->get('sortable_position', 'position')->willReturn('myPosition');
        $this->getSortablePosition()->shouldReturn('myPosition');
    }
}
