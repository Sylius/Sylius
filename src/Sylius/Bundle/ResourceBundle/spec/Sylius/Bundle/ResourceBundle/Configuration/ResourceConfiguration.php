<?php

namespace spec\Sylius\Bundle\ResourceBundle\Configuration;

use PHPSpec2\ObjectBehavior;

/**
 * Resource configuration.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ResourceConfiguration extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param Symfony\Component\HttpFoundation\ParameterBag $attributes
     */
    function let($request, $attributes)
    {
        $this->beConstructedWith('sylius_resource', 'spec', 'SyliusResourceBundle:Test');

        $request->attributes = $attributes;
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Configuration\ResourceConfiguration');
    }

    function it_should_return_assigned_bundle_prefix()
    {
        $this->getBundlePrefix()->shouldReturn('sylius_resource');
    }

    function it_should_return_assigned_resource_name()
    {
        $this->getResourceName()->shouldReturn('spec');
    }

    function it_should_return_assigned_template_namespace()
    {
        $this->getTemplateNamespace()->shouldReturn('SyliusResourceBundle:Test');
    }

    function it_should_generate_correct_service_name()
    {
        $this->getServiceName('manager')->shouldReturn('sylius_resource.manager.spec');
        $this->getServiceName('repository')->shouldReturn('sylius_resource.repository.spec');
        $this->getServiceName('controller')->shouldReturn('sylius_resource.controller.spec');
    }

    function it_should_return_id_as_the_default_identifier()
    {
        $this->getIdentifier()->shouldReturn('id');
    }

    function it_should_not_recognize_collection_as_sortable_by_default()
    {
        $this->isCollectionSortable()->shouldReturn(false);
    }

    function it_should_not_recognize_collection_as_filterable_by_default()
    {
        $this->isCollectionFilterable()->shouldReturn(false);
    }

    function it_should_return_default_collection_limit_which_is_10($request, $attributes)
    {
        $this->getCollectionLimit()->shouldReturn(10);
    }

    function it_should_recognize_collection_as_paginated_by_default($request, $attributes)
    {
        $this->isCollectionPaginated()->shouldReturn(true);
    }

    function it_should_return_default_collection_pagination_max_per_page_which_is_10()
    {
        $this->getPaginationMaxPerPage()->shouldReturn(10);
    }

    function it_should_complain_if_source_is_not_a_request_or_array()
    {
        $this
            ->shouldThrow(new \InvalidArgumentException('Resource configuration source should be an array or Request object'))
            ->duringLoad(123)
        ;

        $this
            ->shouldThrow(new \InvalidArgumentException('Resource configuration source should be an array or Request object'))
            ->duringLoad('abc')
        ;
    }

    function it_should_load_configuration_from_request($request, $attributes)
    {
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled();

        $this->load($request);
    }

    function it_should_complain_if_trying_to_check_request_type_when_request_is_unknown()
    {
        $this
            ->shouldThrow(new \BadMethodCallException('Request is unknown, cannot check its format'))
            ->duringIsHtmlRequest()
        ;
    }

    function it_should_recognize_request_as_html_when_its_the_format($request)
    {
        $request->getRequestFormat()->willReturn('html');

        $this->load($request);
        $this->isHtmlRequest()->shouldReturn(true);
    }

    function it_should_not_recognize_request_as_html_when_its_not_the_format($request)
    {
        $request->geRequestFormat()->willReturn('json');

        $this->load($request);
        $this->isHtmlRequest()->shouldReturn(false);
    }

    function it_should_get_identifier_name_from_request_attributes($request, $attributes)
    {
        $configuration = array('identifier' => 'slug');
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getIdentifier()->shouldReturn('slug');
    }

    function it_should_get_identifier_value_from_request($request, $attributes)
    {
        $configuration = array('identifier' => 'slug');
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $request->get('slug')->willReturn('test-slug');

        $this->load($request);
        $this->getIdentifierValue()->shouldReturn('test-slug');
    }

    function it_should_complain_if_trying_to_get_identifier_criteria_without_request_being_known()
    {
        $this
            ->shouldThrow(new \BadMethodCallException('Request is unknown, cannot get single resource criteria'))
            ->duringGetIdentifierCriteria()
        ;
    }

    function it_should_get_identifier_criteria_from_request($request, $attributes)
    {
        $configuration = array('identifier' => 'slug');
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $request->get('slug')->willReturn('test-slug');

        $this->load($request);
        $this->getIdentifierCriteria()->shouldReturn(array('slug' => 'test-slug'));
    }

    function it_should_get_template_from_request_attributes($request, $attributes)
    {
        $configuration = array('template' => 'SyliusResourceBundle:Test:custom.html.twig');
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getTemplate()->shouldReturn('SyliusResourceBundle:Test:custom.html.twig');
    }

    function it_should_get_form_type_from_request_attributes($request, $attributes)
    {
        $configuration = array('form' => 'sylius_resource_spec');
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getFormType()->shouldReturn('sylius_resource_spec');
    }

    function it_should_get_redirect_from_request_attributes($request, $attributes)
    {
        $configuration = array('redirect' => 'sylius_resource_list');
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getRedirect()->shouldReturn('sylius_resource_list');
    }

    function it_should_get_criteria_from_request_attributes($request, $attributes)
    {
        $configuration = array('criteria' => array('enabled' => false));
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getCriteria()->shouldReturn(array('enabled' => false));
    }

    function it_should_get_criteria_from_request_if_collection_is_filterable($request, $attributes)
    {
        $configuration = array('criteria' => array('enabled' => false), 'filterable' => true);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $request->get('criteria', ANY_ARGUMENT)->willReturn(array('locked' => true));

        $this->load($request);
        $this->getCriteria()->shouldReturn(array('locked' => true));
    }

    function it_should_not_get_criteria_from_request_if_collection_isnt_filterable($request, $attributes)
    {
        $configuration = array('criteria' => array('enabled' => false), 'filterable' => false);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $request->get('criteria', ANY_ARGUMENT)->willReturn(array('locked' => true));

        $this->load($request);
        $this->getCriteria()->shouldReturn(array('enabled' => false));
    }

    function it_should_get_sorting_from_request_if_collection_is_sortable($request, $attributes)
    {
        $configuration = array('sorting' => array('name' => 'asc'), 'sortable' => true);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $request->get('sorting', ANY_ARGUMENT)->willReturn(array('createdAt' => 'desc'));

        $this->load($request);
        $this->getSorting()->shouldReturn(array('createdAt' => 'desc'));
    }

    function it_should_not_get_sorting_from_request_if_collection_isnt_sortable($request, $attributes)
    {
        $configuration = array('sorting' => array('name' => 'asc'), 'sortable' => false);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $request->get('sorting', ANY_ARGUMENT)->willReturn(array('createdAt' => 'desc'));

        $this->load($request);
        $this->getSorting()->shouldReturn(array('name' => 'asc'));
    }

    function it_should_get_sorting_from_request_attributes($request, $attributes)
    {
        $configuration = array('sorting' => array('createdAt' => 'asc'));
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getSorting()->shouldReturn(array('createdAt' => 'asc'));
    }

    function it_should_recognize_collection_as_not_paginated_from_request_attributes($request, $attributes)
    {
        $configuration = array('paginate' => false);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->isCollectionPaginated()->shouldReturn(false);
    }

    function it_should_return_collection_pagination_max_per_page_from_request_attributes($request, $attributes)
    {
        $configuration = array('paginate' => 25);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getPaginationMaxPerPage()->shouldReturn(25);
    }

    function it_should_return_collection_limit_from_request_attributes($request, $attributes)
    {
        $configuration = array('limit' => 20);
        $attributes->get('_sylius.resource', ANY_ARGUMENT)->shouldBeCalled()->willReturn($configuration);

        $this->load($request);
        $this->getCollectionLimit()->shouldReturn(20);
    }

    function it_should_load_configuration_from_array()
    {
        $this->load(array());
    }

    function it_should_get_template_from_array_configuration()
    {
        $configuration = array('template' => 'SyliusResourceBundle:Test:custom.html.twig');

        $this->load($configuration);
        $this->getTemplate()->shouldReturn('SyliusResourceBundle:Test:custom.html.twig');
    }

    function it_should_get_form_type_from_array_configuration()
    {
        $configuration = array('form' => 'sylius_resource_spec');

        $this->load($configuration);
        $this->getFormType()->shouldReturn('sylius_resource_spec');
    }

    function it_should_get_redirect_from_array_configuration()
    {
        $configuration = array('redirect' => 'sylius_resource_list');

        $this->load($configuration);
        $this->getRedirect()->shouldReturn('sylius_resource_list');
    }

    function it_should_get_criteria_from_array_configuration()
    {
        $configuration = array('criteria' => array('enabled' => false));

        $this->load($configuration);
        $this->getCriteria()->shouldReturn(array('enabled' => false));
    }

    function it_should_get_sorting_from_array_configuration()
    {
        $configuration = array('sorting' => array('createdAt' => 'asc'));

        $this->load($configuration);
        $this->getSorting()->shouldReturn(array('createdAt' => 'asc'));
    }

    function it_should_recognize_collection_as_not_paginated_from_array_configuration()
    {
        $configuration = array('paginate' => false);

        $this->load($configuration);
        $this->isCollectionPaginated()->shouldReturn(false);
    }

    function it_should_return_collection_pagination_max_per_page_from_array_configuration()
    {
        $configuration = array('paginate' => 25);

        $this->load($configuration);
        $this->getPaginationMaxPerPage()->shouldReturn(25);
    }

    function it_should_return_collection_limit_from_array_configuration()
    {
        $configuration = array('limit' => 20);

        $this->load($configuration);
        $this->getCollectionLimit()->shouldReturn(20);
    }
}
