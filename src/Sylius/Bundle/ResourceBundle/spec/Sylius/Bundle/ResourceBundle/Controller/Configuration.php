<?php

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PHPSpec2\ObjectBehavior;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Resource controller configuration spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Configuration extends ObjectBehavior
{
    /**
     * @param Symfony\Component\HttpFoundation\Request $request
     */
    function let($request)
    {
        $this->beConstructedWith('sylius_resource', 'spec', 'SyliusResourceBundle:Test');

        $request->attributes = new ParameterBag();
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\Configuration');
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

    function it_should_return_default_collection_limit_which_is_10()
    {
        $this->getCollectionLimit()->shouldReturn(10);
    }

    function it_should_recognize_collection_as_paginated_by_default()
    {
        $this->isCollectionPaginated()->shouldReturn(true);
    }

    function it_should_return_default_collection_pagination_max_per_page_which_is_10()
    {
        $this->getPaginationMaxPerPage()->shouldReturn(10);
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

        $this->setRequest($request);
        $this->isHtmlRequest()->shouldReturn(true);
    }

    function it_should_not_recognize_request_as_html_when_its_not_the_format($request)
    {
        $request->geRequestFormat()->willReturn('json');

        $this->setRequest($request);
        $this->isHtmlRequest()->shouldReturn(false);
    }

    function it_should_get_identifier_name_from_request_attributes($request)
    {
        $request->attributes->set('identifier', 'slug');

        $this->setRequest($request);
        $this->getIdentifier()->shouldReturn('slug');
    }

    function it_should_get_identifier_value_from_request($request)
    {
        $request->get('id')->willReturn('test-slug');

        $this->setRequest($request);
        $this->getIdentifierValue()->shouldReturn('test-slug');
    }

    function it_should_complain_if_trying_to_get_identifier_criteria_without_request_being_known()
    {
        $this
            ->shouldThrow(new \BadMethodCallException('Request is unknown, cannot get single resource criteria'))
            ->duringGetIdentifierCriteria()
        ;
    }

    function it_should_get_identifier_criteria_from_request($request)
    {
        $request->attributes->set('identifier', 'slug');
        $request->get('slug')->willReturn('test-slug');

        $this->setRequest($request);
        $this->getIdentifierCriteria()->shouldReturn(array('slug' => 'test-slug'));
    }

    function it_should_get_template_from_request_attributes($request)
    {
        $request->attributes->set('_template', 'SyliusResourceBundle:Test:custom.html.twig');

        $this->setRequest($request);
        $this->getTemplate()->shouldReturn('SyliusResourceBundle:Test:custom.html.twig');
    }

    function it_should_get_form_type_from_request_attributes()
    {
        $this->getFormType()->shouldReturn('sylius_resource_spec');
    }

    function it_should_get_redirect_from_request_attributes($request)
    {
        $request->attributes->set('_redirect', 'sylius_resource_list');

        $this->setRequest($request);
        $this->getRedirect()->shouldReturn('sylius_resource_list');
    }

    function it_should_get_criteria_from_request_attributes($request)
    {
        $request->attributes->set('_criteria', array('enabled' => false));

        $this->setRequest($request);
        $this->getCriteria()->shouldReturn(array('enabled' => false));
    }

    function it_should_get_criteria_from_request_if_collection_is_filterable($request)
    {
        $request->get('_criteria', ANY_ARGUMENT)->shouldBeCalled()->willReturn(array('locked' => false));
        $request->attributes->set('_criteria', array('enabled' => false));
        $request->attributes->set('_filterable', true);

        $this->setRequest($request);
        $this->getCriteria()->shouldReturn(array('locked' => false));
    }

    function it_should_not_get_criteria_from_request_if_collection_isnt_filterable($request)
    {
        $request->get('_criteria', ANY_ARGUMENT)->shouldNotBeCalled();
        $request->attributes->set('_criteria', array('enabled' => false));
        $request->attributes->set('_filterable', false);

        $this->setRequest($request);
        $this->getCriteria()->shouldReturn(array('enabled' => false));
    }

    function it_should_get_sorting_from_request_if_collection_is_sortable($request)
    {
        $request->get('_sorting', ANY_ARGUMENT)->willReturn(array('createdAt' => 'desc'));
        $request->attributes->set('_sorting', array('name' => 'asc'));
        $request->attributes->set('_sortable', true);

        $this->setRequest($request);
        $this->getSorting()->shouldReturn(array('createdAt' => 'desc'));
    }

    function it_should_not_get_sorting_from_request_if_collection_isnt_sortable($request)
    {
        $request->get('_sorting', ANY_ARGUMENT)->shouldNotBeCalled();
        $request->attributes->set('_sorting', array('name' => 'asc'));
        $request->attributes->set('_sortable', false);

        $this->setRequest($request);
        $this->getSorting()->shouldReturn(array('name' => 'asc'));
    }

    function it_should_get_sorting_from_request_attributes($request)
    {
        $request->attributes->set('_sorting', array('createdAt' => 'asc'));

        $this->setRequest($request);
        $this->getSorting()->shouldReturn(array('createdAt' => 'asc'));
    }

    function it_should_recognize_collection_as_not_paginated_from_request_attributes($request)
    {
        $request->attributes->set('_paginate', false);

        $this->setRequest($request);
        $this->isCollectionPaginated()->shouldReturn(false);
    }

    function it_should_return_collection_pagination_max_per_page_from_request_attributes($request)
    {
        $request->attributes->set('_paginate', 25);

        $this->setRequest($request);
        $this->getPaginationMaxPerPage()->shouldReturn(25);
    }

    function it_should_return_collection_limit_from_request_attributes($request, $attributes)
    {
        $request->attributes->set('_limit', 20);

        $this->setRequest($request);
        $this->getCollectionLimit()->shouldReturn(20);
    }
}
