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
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin RequestConfiguration
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
class RequestConfigurationSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata, Request $request, Parameters $parameters)
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

    function it_has_metadata(MetadataInterface $metadata)
    {
        $this->getMetadata()->shouldReturn($metadata);
    }
    
    function it_has_parameters(Parameters $parameters)
    {
        $this->getParameters()->shouldReturn($parameters);
    }

    function it_checks_if_its_a_html_request(Request $request)
    {
        $request->getRequestFormat()->willReturn('html');
        $this->isHtmlRequest()->shouldReturn(true);

        $request->getRequestFormat()->willReturn('json');
        $this->isHtmlRequest()->shouldReturn(false);
    }

    function it_returns_default_template_names(MetadataInterface $metadata)
    {
        $metadata->getTemplatesNamespace()->willReturn('SyliusAdminBundle:Product');

        $this->getDefaultTemplate('index.html')->shouldReturn('SyliusAdminBundle:Product:index.html.twig');
        $this->getDefaultTemplate('show.html')->shouldReturn('SyliusAdminBundle:Product:show.html.twig');
        $this->getDefaultTemplate('create.html')->shouldReturn('SyliusAdminBundle:Product:create.html.twig');
        $this->getDefaultTemplate('update.html')->shouldReturn('SyliusAdminBundle:Product:update.html.twig');
        $this->getDefaultTemplate('custom.html')->shouldReturn('SyliusAdminBundle:Product:custom.html.twig');
    }

    function it_takes_the_custom_template_if_specified(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getTemplatesNamespace()->willReturn('SyliusAdminBundle:Product');
        $parameters->get('template', 'SyliusAdminBundle:Product:foo.html.twig')->willReturn('AppBundle:Product:show.html.twig');

        $this->getTemplate('foo.html')->shouldReturn('AppBundle:Product:show.html.twig');
    }

    function it_generates_form_type(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $parameters->get('form', 'sylius_product')->willReturn('sylius_product');
        $this->getFormType()->shouldReturn('sylius_product');

        $parameters->get('form', 'sylius_product')->willReturn('sylius_product_pricing');
        $this->getFormType()->shouldReturn('sylius_product_pricing');
    }

    function it_generates_route_names(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $parameters->get('section')->willReturn(null);

        $this->getRouteName('index')->shouldReturn('sylius_product_index');
        $this->getRouteName('show')->shouldReturn('sylius_product_show');
        $this->getRouteName('custom')->shouldReturn('sylius_product_custom');

        $parameters->get('section')->willReturn('admin');
        $this->getRouteName('index')->shouldReturn('sylius_admin_product_index');
        $this->getRouteName('show')->shouldReturn('sylius_admin_product_show');
        $this->getRouteName('custom')->shouldReturn('sylius_admin_product_custom');
    }

    function it_generates_redirect_referer(Parameters $parameters, Request $request, ParameterBag $bag)
    {
        $request->headers = $bag;
        $bag->get('referer')->willReturn('http://myurl.com');

        $parameters->get('redirect')->willReturn(array('referer' => 'http://myurl.com'));

        $this->getRedirectReferer()->shouldReturn('http://myurl.com');
    }

    function it_generates_redirect_route(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $parameters->get('section')->willReturn(null);

        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectRoute('index')->shouldReturn('sylius_product_index');

        $parameters->get('redirect')->willReturn(array('route' => 'myRoute'));
        $this->getRedirectRoute('show')->shouldReturn('myRoute');

        $parameters->get('redirect')->willReturn('myRoute');
        $this->getRedirectRoute('custom')->shouldReturn('myRoute');
    }

    function it_takes_section_into_account_when_generating_redirect_route(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $parameters->get('section')->willReturn('admin');

        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectRoute('index')->shouldReturn('sylius_admin_product_index');

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

        $parameters->get('redirect')->willReturn(array('parameters' => array('myParameter')));

        $this->getRedirectParameters('resource')->shouldReturn(array('myParameter'));
    }

    function it_checks_if_limit_is_enabled(Parameters $parameters)
    {
        $parameters->get('limit', Argument::any())->willReturn(10);
        $this->isLimited()->shouldReturn(true);

        $parameters->get('limit', Argument::any())->willReturn(null);
        $this->isLimited()->shouldReturn(false);
    }

    function it_gets_limit(Parameters $parameters)
    {
        $parameters->get('limit', false)->willReturn(true);
        $parameters->get('limit', 10)->willReturn(10);
        $this->getLimit()->shouldReturn(10);

        $parameters->get('limit', false)->willReturn(false);
        $parameters->get('limit', 10)->willReturn(null);
        $this->getLimit()->shouldReturn(null);
    }

    function it_checks_if_pagination_is_enabled(Parameters $parameters)
    {
        $parameters->get('paginate', Argument::any())->willReturn(10);
        $this->isPaginated()->shouldReturn(true);

        $parameters->get('paginate', Argument::any())->willReturn(null);
        $this->isPaginated()->shouldReturn(false);
    }

    function it_gets_pagination_max_per_page(Parameters $parameters)
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
        $parameters->has('repository')->willReturn(false);
        $this->getRepositoryMethod()->shouldReturn(null);

        $parameters->has('repository')->willReturn(true);
        $parameters->get('repository')->willReturn(array('method' => 'findAllEnabled'));

        $this->getRepositoryMethod()->shouldReturn('findAllEnabled');
    }

    function it_has_repository_arguments_parameter(Parameters $parameters)
    {
        $parameters->has('repository')->willReturn(false);
        $this->getRepositoryArguments()->shouldReturn(array());

        $repositoryConfiguration = array('arguments' => 'value');
        $parameters->has('repository')->willReturn(true);
        $parameters->get('repository')->willReturn($repositoryConfiguration);

        $this->getRepositoryArguments()->shouldReturn(array('value'));

        $repositoryConfiguration = array('arguments' => array('foo, bar'));
        $parameters->has('repository')->willReturn(true);
        $parameters->get('repository')->willReturn($repositoryConfiguration);

        $this->getRepositoryArguments()->shouldReturn(array('foo, bar'));
    }

    function it_has_factory_method_parameter(Parameters $parameters)
    {
        $parameters->has('factory')->willReturn(false);
        $this->getFactoryMethod()->shouldReturn(null);

        $parameters->has('factory')->willReturn(true);
        $parameters->get('factory')->willReturn(array('method' => 'createForPromotion'));

        $this->getFactoryMethod()->shouldReturn('createForPromotion');
    }

    function it_has_factory_arguments_parameter(Parameters $parameters)
    {
        $parameters->has('factory')->willReturn(false);
        $this->getFactoryArguments()->shouldReturn(array());

        $factoryConfiguration = array('arguments' => 'value');
        $parameters->has('factory')->willReturn(true);
        $parameters->get('factory')->willReturn($factoryConfiguration);

        $this->getFactoryArguments()->shouldReturn(array('value'));

        $factoryConfiguration = array('arguments' => array('foo, bar'));
        $parameters->has('factory')->willReturn(true);
        $parameters->get('factory')->willReturn($factoryConfiguration);

        $this->getFactoryArguments()->shouldReturn(array('foo, bar'));
    }

    function it_has_flash_message_parameter(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

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
    
    function it_has_permission_unless_defined_as_false_in_parameters(Parameters $parameters)
    {
        $parameters->has('permission')->willReturn(false);
        $this->shouldHavePermission();

        $parameters->has('permission')->willReturn(true);
        $parameters->get('permission')->willReturn('custom_permission');
        $this->shouldHavePermission();

        $parameters->has('permission')->willReturn(true);
        $parameters->get('permission')->willReturn(false);
        $this->shouldNotHavePermission();
    }
    
    function it_generates_permission_name(MetadataInterface $metadata, Parameters $parameters)
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        
        $parameters->has('permission')->willReturn(false);

        $this->getPermission('index')->shouldReturn('sylius.product.index');
    }

    function it_takes_permission_name_from_parameters_if_provided(Parameters $parameters)
    {
        $parameters->has('permission')->willReturn(true);
        $parameters->get('permission')->willReturn('app.sales_order.view_pricing');

        $this->getPermission('index')->shouldReturn('app.sales_order.view_pricing');
    }

    function it_throws_an_exception_when_permission_is_set_as_false_in_parameters_but_still_trying_to_get_it(Parameters $parameters)
    {
        $parameters->has('permission')->willReturn(true);
        $parameters->get('permission')->willReturn(false);

        $this
            ->shouldThrow(\LogicException::class)
            ->during('getPermission', array('index'))
        ;
    }
    
    function it_has_event_name(Parameters $parameters)
    {
        $parameters->get('event')->willReturn('foo');
        $this->getEvent()->shouldReturn('foo');
    }

    function it_has_section(Parameters $parameters)
    {
        $parameters->get('section')->willReturn(null);
        $this->getSection()->shouldReturn(null);

        $parameters->get('section')->willReturn('admin');
        $this->getSection()->shouldReturn('admin');
    }
}
