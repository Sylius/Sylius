<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\Parameters;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Arnaud Langade <arn0d.dev@gmail.com>
 */
final class RequestConfigurationSpec extends ObjectBehavior
{
    function let(MetadataInterface $metadata, Request $request, Parameters $parameters): void
    {
        $this->beConstructedWith($metadata, $request, $parameters);
    }

    function it_has_request(Request $request): void
    {
        $this->getRequest()->shouldReturn($request);
    }

    function it_has_metadata(MetadataInterface $metadata): void
    {
        $this->getMetadata()->shouldReturn($metadata);
    }

    function it_has_parameters(Parameters $parameters): void
    {
        $this->getParameters()->shouldReturn($parameters);
    }

    function it_checks_if_its_a_html_request(Request $request): void
    {
        $request->getRequestFormat()->willReturn('html');
        $this->isHtmlRequest()->shouldReturn(true);

        $request->getRequestFormat()->willReturn('json');
        $this->isHtmlRequest()->shouldReturn(false);
    }

    function it_returns_default_template_names(MetadataInterface $metadata): void
    {
        $metadata->getTemplatesNamespace()->willReturn('SyliusAdminBundle:Product');

        $this->getDefaultTemplate('index.html')->shouldReturn('SyliusAdminBundle:Product:index.html.twig');
        $this->getDefaultTemplate('show.html')->shouldReturn('SyliusAdminBundle:Product:show.html.twig');
        $this->getDefaultTemplate('create.html')->shouldReturn('SyliusAdminBundle:Product:create.html.twig');
        $this->getDefaultTemplate('update.html')->shouldReturn('SyliusAdminBundle:Product:update.html.twig');
        $this->getDefaultTemplate('custom.html')->shouldReturn('SyliusAdminBundle:Product:custom.html.twig');
    }

    function it_returns_default_template_names_for_a_directory_based_templates(MetadataInterface $metadata): void
    {
        $metadata->getTemplatesNamespace()->willReturn('book/Backend');

        $this->getDefaultTemplate('index.html')->shouldReturn('book/Backend/index.html.twig');
        $this->getDefaultTemplate('show.html')->shouldReturn('book/Backend/show.html.twig');
        $this->getDefaultTemplate('create.html')->shouldReturn('book/Backend/create.html.twig');
        $this->getDefaultTemplate('update.html')->shouldReturn('book/Backend/update.html.twig');
        $this->getDefaultTemplate('custom.html')->shouldReturn('book/Backend/custom.html.twig');
    }

    function it_takes_the_custom_template_if_specified(MetadataInterface $metadata, Parameters $parameters): void
    {
        $metadata->getTemplatesNamespace()->willReturn('SyliusAdminBundle:Product');
        $parameters->get('template', 'SyliusAdminBundle:Product:foo.html.twig')->willReturn('AppBundle:Product:show.html.twig');

        $this->getTemplate('foo.html')->shouldReturn('AppBundle:Product:show.html.twig');
    }

    function it_gets_form_type_and_its_options(MetadataInterface $metadata, Parameters $parameters): void
    {
        $parameters->get('form')->willReturn(['type' => 'sylius_custom_resource']);
        $this->getFormType()->shouldReturn('sylius_custom_resource');
        $this->getFormOptions()->shouldReturn([]);

        $parameters->get('form')->willReturn('sylius_custom_resource');
        $this->getFormType()->shouldReturn('sylius_custom_resource');
        $this->getFormOptions()->shouldReturn([]);

        $parameters->get('form')->willReturn(['type' => 'sylius_custom_resource', 'options' => ['key' => 'value']]);
        $this->getFormType()->shouldReturn('sylius_custom_resource');
        $this->getFormOptions()->shouldReturn(['key' => 'value']);

        $metadata->getClass('form')->willReturn('\Fully\Qualified\ClassName');
        $parameters->get('form')->willReturn([]);
        $this->getFormType()->shouldReturn('\Fully\Qualified\ClassName');
        $this->getFormOptions()->shouldReturn([]);

        $metadata->getClass('form')->willReturn('\Fully\Qualified\ClassName');
        $parameters->get('form')->willReturn(['options' => ['key' => 'value']]);
        $this->getFormType()->shouldReturn('\Fully\Qualified\ClassName');
        $this->getFormOptions()->shouldReturn(['key' => 'value']);
    }

    function it_generates_form_type_with_array_configuration(MetadataInterface $metadata, Parameters $parameters): void
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $parameters->get('form')->willReturn(['type'=> 'sylius_product', 'options' => ['validation_groups' => ['sylius']]]);
        $this->getFormType()->shouldReturn('sylius_product');
        $this->getFormOptions()->shouldReturn(['validation_groups' => ['sylius']]);
    }

    function it_generates_route_names(MetadataInterface $metadata, Parameters $parameters): void
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

    function it_generates_redirect_referer(Parameters $parameters, Request $request, ParameterBag $bag): void
    {
        $request->headers = $bag;
        $bag->get('referer')->willReturn('http://myurl.com');

        $parameters->get('redirect')->willReturn(['referer' => 'http://myurl.com']);

        $this->getRedirectReferer()->shouldReturn('http://myurl.com');
    }

    function it_generates_redirect_route(MetadataInterface $metadata, Parameters $parameters): void
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $parameters->get('section')->willReturn(null);

        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectRoute('index')->shouldReturn('sylius_product_index');

        $parameters->get('redirect')->willReturn(['route' => 'myRoute']);
        $this->getRedirectRoute('show')->shouldReturn('myRoute');

        $parameters->get('redirect')->willReturn('myRoute');
        $this->getRedirectRoute('custom')->shouldReturn('myRoute');
    }

    function it_takes_section_into_account_when_generating_redirect_route(MetadataInterface $metadata, Parameters $parameters): void
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');
        $parameters->get('section')->willReturn('admin');

        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectRoute('index')->shouldReturn('sylius_admin_product_index');

        $parameters->get('redirect')->willReturn(['route' => 'myRoute']);
        $this->getRedirectRoute('show')->shouldReturn('myRoute');

        $parameters->get('redirect')->willReturn('myRoute');
        $this->getRedirectRoute('custom')->shouldReturn('myRoute');
    }

    function it_returns_array_as_redirect_parameters(Parameters $parameters): void
    {
        $parameters->get('vars', [])->willReturn([]);
        $this->getVars()->shouldReturn([]);

        $parameters->get('redirect')->willReturn(null);
        $this->getRedirectParameters()->shouldReturn([]);

        $parameters->get('redirect')->willReturn('string');
        $this->getRedirectParameters()->shouldReturn([]);

        $parameters->get('redirect')->willReturn(['parameters' => []]);
        $this->getRedirectParameters()->shouldReturn([]);

        $parameters->get('redirect')->willReturn(['parameters' => ['myParameter']]);
        $this->getRedirectParameters()->shouldReturn(['myParameter']);

        $parameters->get('redirect')->willReturn(['parameters' => ['myParameter']]);
        $this->getRedirectParameters('resource')->shouldReturn(['myParameter']);

        $invalidExtraParameters = ['redirect' => ['parameters' => 'myValue']];
        $parameters->get('vars', [])->willReturn($invalidExtraParameters);
        $this->getVars()->shouldReturn($invalidExtraParameters);
        $parameters->get('redirect')->willReturn(['parameters' => ['myParameter']]);
        $this->getRedirectParameters('resource')->shouldReturn(['myParameter']);

        $validExtraParameters = ['redirect' => ['parameters' => ['myExtraParameter']]];
        $parameters->get('vars', [])->willReturn($validExtraParameters);
        $this->getVars()->shouldReturn($validExtraParameters);
        $parameters->get('redirect')->willReturn(['parameters' => ['myParameter']]);
        $this->getRedirectParameters('resource')->shouldReturn(['myParameter', 'myExtraParameter']);
    }

    function it_checks_if_limit_is_enabled(Parameters $parameters): void
    {
        $parameters->get('limit', Argument::any())->willReturn(10);
        $this->isLimited()->shouldReturn(true);

        $parameters->get('limit', Argument::any())->willReturn(null);
        $this->isLimited()->shouldReturn(false);
    }

    function it_gets_limit(Parameters $parameters): void
    {
        $parameters->get('limit', false)->willReturn(true);
        $parameters->get('limit', 10)->willReturn(10);
        $this->getLimit()->shouldReturn(10);

        $parameters->get('limit', false)->willReturn(false);
        $parameters->get('limit', 10)->willReturn(null);
        $this->getLimit()->shouldReturn(null);
    }

    function it_checks_if_pagination_is_enabled(Parameters $parameters): void
    {
        $parameters->get('paginate', Argument::any())->willReturn(10);
        $this->isPaginated()->shouldReturn(true);

        $parameters->get('paginate', Argument::any())->willReturn(null);
        $this->isPaginated()->shouldReturn(false);
    }

    function it_gets_pagination_max_per_page(Parameters $parameters): void
    {
        $parameters->get('paginate', 10)->willReturn(20);
        $this->getPaginationMaxPerPage()->shouldReturn(20);

        $parameters->get('paginate', 10)->willReturn(10);
        $this->getPaginationMaxPerPage()->shouldReturn(10);
    }

    function it_checks_if_the_resource_is_filterable(Parameters $parameters): void
    {
        $parameters->get('filterable', Argument::any())->willReturn(true);
        $this->isFilterable()->shouldReturn(true);

        $parameters->get('filterable', Argument::any())->willReturn(null);
        $this->isFilterable()->shouldReturn(false);
    }

    function it_has_no_filterable_parameter(Parameters $parameters): void
    {
        $defaultCriteria = ['property' => 'myValue'];

        $parameters->get('criteria', Argument::any())->willReturn([]);
        $parameters->get('filterable', false)->willReturn(false);

        $this->getCriteria($defaultCriteria)->shouldBeArray();
        $this->getCriteria($defaultCriteria)->shouldHaveCount(1);
    }

    function it_has_criteria_parameter(Parameters $parameters, Request $request): void
    {
        $criteria = ['property' => 'myNewValue'];

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', Argument::any())->willReturn([]);
        $request->get('criteria', [])->willReturn($criteria);
        $this->getCriteria()->shouldReturn($criteria);
    }

    function it_allows_to_override_criteria_parameter_in_route(Parameters $parameters, Request $request): void
    {
        $criteria = ['property' => 'myValue'];
        $overriddenCriteria = ['other_property' => 'myNewValue'];
        $combinedCriteria = ['property' => 'myValue', 'other_property' => 'myNewValue'];

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', [])->willReturn($criteria);
        $request->get('criteria', [])->willReturn($overriddenCriteria);

        $this->getCriteria()->shouldReturn($combinedCriteria);

        $defaultCriteria = ['slug' => 'foo'];
        $combinedDefaultCriteria = ['property' => 'myValue', 'slug' => 'foo', 'other_property' => 'myNewValue'];

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', Argument::any())->willReturn($criteria);
        $request->get('criteria', [])->willReturn($overriddenCriteria);

        $this->getCriteria($defaultCriteria)->shouldReturn($combinedDefaultCriteria);

        $parameters->get('filterable', false)->willReturn(true);
        $parameters->get('criteria', [])->willReturn(['filter' => 'route']);
        $request->get('criteria', [])->willReturn(['filter' => 'request']);

        $this->getCriteria(['filter' => 'default'])->shouldReturn(['filter' => 'request']);
    }

    function it_checks_if_the_resource_is_sortable(Parameters $parameters): void
    {
        $parameters->get('sortable', Argument::any())->willReturn(true);
        $this->isSortable()->shouldReturn(true);

        $parameters->get('sortable', Argument::any())->willReturn(null);
        $this->isSortable()->shouldReturn(false);
    }

    function it_has_sorting_parameter(Parameters $parameters, Request $request): void
    {
        $sorting = ['property' => 'asc'];

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', Argument::any())->willReturn($sorting);
        $request->get('sorting', [])->willReturn($sorting);

        $this->getSorting()->shouldReturn($sorting);
    }

    function it_has_no_sortable_parameter(Parameters $parameters): void
    {
        $defaultSorting = ['property' => 'desc'];

        $parameters->get('sorting', Argument::any())->willReturn([]);
        $parameters->get('sortable', false)->willReturn(false);

        $this->getSorting($defaultSorting)->shouldBeArray();
        $this->getSorting($defaultSorting)->shouldHaveCount(1);
    }

    function it_allows_to_override_sorting_parameter_in_route(Parameters $parameters, Request $request): void
    {
        $sorting = ['property' => 'desc'];
        $overriddenSorting = ['other_property' => 'asc'];
        $combinedSorting = ['other_property' => 'asc', 'property' => 'desc'];

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', [])->willReturn($sorting);
        $request->get('sorting', [])->willReturn($overriddenSorting);

        $this->getSorting()->shouldReturn($combinedSorting);

        $defaultSorting = ['foo' => 'bar'];
        $combinedDefaultSorting = ['other_property' => 'asc', 'property' => 'desc', 'foo' => 'bar'];

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', Argument::any())->willReturn($sorting);
        $request->get('sorting', [])->willReturn($overriddenSorting);

        $this->getSorting($defaultSorting)->shouldReturn($combinedDefaultSorting);

        $parameters->get('sortable', false)->willReturn(true);
        $parameters->get('sorting', [])->willReturn(['sort' => 'route']);
        $request->get('sorting', [])->willReturn(['sort' => 'request']);

        $this->getSorting(['sort' => 'default'])->shouldReturn(['sort' => 'request']);
    }

    function it_has_repository_method_parameter(Parameters $parameters): void
    {
        $parameters->has('repository')->willReturn(false);
        $this->getRepositoryMethod()->shouldReturn(null);

        $parameters->has('repository')->willReturn(true);
        $parameters->get('repository')->willReturn(['method' => 'findAllEnabled']);

        $this->getRepositoryMethod()->shouldReturn('findAllEnabled');
    }

    function it_has_repository_arguments_parameter(Parameters $parameters): void
    {
        $parameters->has('repository')->willReturn(false);
        $this->getRepositoryArguments()->shouldReturn([]);

        $repositoryConfiguration = ['arguments' => 'value'];
        $parameters->has('repository')->willReturn(true);
        $parameters->get('repository')->willReturn($repositoryConfiguration);

        $this->getRepositoryArguments()->shouldReturn(['value']);

        $repositoryConfiguration = ['arguments' => ['foo, bar']];
        $parameters->has('repository')->willReturn(true);
        $parameters->get('repository')->willReturn($repositoryConfiguration);

        $this->getRepositoryArguments()->shouldReturn(['foo, bar']);
    }

    function it_has_factory_method_parameter(Parameters $parameters): void
    {
        $parameters->has('factory')->willReturn(false);
        $this->getFactoryMethod()->shouldReturn(null);

        $parameters->has('factory')->willReturn(true);
        $parameters->get('factory')->willReturn(['method' => 'createForPromotion']);

        $this->getFactoryMethod()->shouldReturn('createForPromotion');
    }

    function it_has_factory_arguments_parameter(Parameters $parameters): void
    {
        $parameters->has('factory')->willReturn(false);
        $this->getFactoryArguments()->shouldReturn([]);

        $factoryConfiguration = ['arguments' => 'value'];
        $parameters->has('factory')->willReturn(true);
        $parameters->get('factory')->willReturn($factoryConfiguration);

        $this->getFactoryArguments()->shouldReturn(['value']);

        $factoryConfiguration = ['arguments' => ['foo, bar']];
        $parameters->has('factory')->willReturn(true);
        $parameters->get('factory')->willReturn($factoryConfiguration);

        $this->getFactoryArguments()->shouldReturn(['foo, bar']);
    }

    function it_has_flash_message_parameter(MetadataInterface $metadata, Parameters $parameters): void
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $parameters->get('flash', 'sylius.product.message')->willReturn('sylius.product.message');
        $this->getFlashMessage('message')->shouldReturn('sylius.product.message');

        $parameters->get('flash', 'sylius.product.flash')->willReturn('sylius.product.myMessage');
        $this->getFlashMessage('flash')->shouldReturn('sylius.product.myMessage');
    }

    function it_has_sortable_position_parameter(Parameters $parameters): void
    {
        $parameters->get('sortable_position', 'position')->willReturn('position');
        $this->getSortablePosition()->shouldReturn('position');

        $parameters->get('sortable_position', 'position')->willReturn('myPosition');
        $this->getSortablePosition()->shouldReturn('myPosition');
    }

    function it_has_permission_unless_defined_as_false_in_parameters(Parameters $parameters): void
    {
        $parameters->get('permission', false)->willReturn(false);
        $this->shouldNotHavePermission();

        $parameters->get('permission', false)->willReturn('custom_permission');
        $this->shouldHavePermission();

        $parameters->get('permission', false)->willReturn(false);
        $this->shouldNotHavePermission();
    }

    function it_generates_permission_name(MetadataInterface $metadata, Parameters $parameters): void
    {
        $metadata->getApplicationName()->willReturn('sylius');
        $metadata->getName()->willReturn('product');

        $parameters->get('permission')->willReturn(true);

        $this->getPermission('index')->shouldReturn('sylius.product.index');
    }

    function it_takes_permission_name_from_parameters_if_provided(Parameters $parameters): void
    {
        $parameters->get('permission')->willReturn('app.sales_order.view_pricing');

        $this->getPermission('index')->shouldReturn('app.sales_order.view_pricing');
    }

    function it_throws_an_exception_when_permission_is_set_as_false_in_parameters_but_still_trying_to_get_it(Parameters $parameters): void
    {
        $parameters->get('permission')->willReturn(null);

        $this
            ->shouldThrow(\LogicException::class)
            ->during('getPermission', ['index'])
        ;
    }

    function it_has_event_name(Parameters $parameters): void
    {
        $parameters->get('event')->willReturn('foo');
        $this->getEvent()->shouldReturn('foo');
    }

    function it_has_section(Parameters $parameters): void
    {
        $parameters->get('section')->willReturn(null);
        $this->getSection()->shouldReturn(null);

        $parameters->get('section')->willReturn('admin');
        $this->getSection()->shouldReturn('admin');
    }

    function it_has_vars(Parameters $parameters): void
    {
        $parameters->get('vars', [])->willReturn(['foo' => 'bar']);
        $this->getVars()->shouldReturn(['foo' => 'bar']);
    }

    function it_does_not_have_grid_unless_defined_as_in_parameters(Parameters $parameters): void
    {
        $parameters->has('grid')->willReturn(false);
        $this->shouldNotHaveGrid();

        $parameters->has('grid')->willReturn(true);
        $this->shouldHaveGrid();

        $parameters->has('grid')->willReturn(true);
        $parameters->get('grid')->willReturn('sylius_admin_tax_category');

        $this->getGrid()->shouldReturn('sylius_admin_tax_category');
    }

    function it_throws_an_exception_when_trying_to_retrieve_undefined_grid(Parameters $parameters): void
    {
        $parameters->has('grid')->willReturn(false);

        $this
            ->shouldThrow(\LogicException::class)
            ->during('getGrid')
        ;
    }

    function it_can_have_state_machine_transition(Parameters $parameters): void
    {
        $parameters->has('state_machine')->willReturn(false);
        $this->hasStateMachine()->shouldReturn(false);

        $parameters->has('state_machine')->willReturn(true);
        $parameters->get('state_machine')->willReturn([
            'graph' => 'sylius_product_review_state',
            'transition' => 'approve',
        ]);

        $this->hasStateMachine()->shouldReturn(true);
        $this->getStateMachineGraph()->shouldReturn('sylius_product_review_state');
        $this->getStateMachineTransition()->shouldReturn('approve');
    }
}
