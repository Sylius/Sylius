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

use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Pagerfanta;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProvider;
use Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProviderInterface;
use Sylius\Component\Contact\Model\Request;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @mixin ResourcesCollectionProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesCollectionProviderSpec extends ObjectBehavior
{
    function let(PagerfantaFactory $pagerfantaRepresentationFactory)
    {
        $this->beConstructedWith($pagerfantaRepresentationFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ResourceBundle\Controller\ResourcesCollectionProvider');
    }
    
    function it_implements_resources_finder_interface()
    {
        $this->shouldImplement(ResourcesCollectionProviderInterface::class);
    }

    function it_gets_all_resources_if_not_paginated_and_there_is_no_limit(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource,
        ResourceInterface $secondResource
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(false);
        
        $repository->findAll()->willReturn(array($firstResource, $secondResource));
        
        $this->get($requestConfiguration, $repository)->shouldReturn(array($firstResource, $secondResource));
    }

    function it_finds_resources_by_criteria_if_not_paginated(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource,
        ResourceInterface $secondResource,
        ResourceInterface $thirdResource
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);
        
        $requestConfiguration->getCriteria()->willReturn(array('custom' => 'criteria'));
        $requestConfiguration->getSorting()->willReturn(array('name' => 'desc'));

        $repository->findBy(array('custom' => 'criteria'), array('name' => 'desc'), 15)->willReturn(array($firstResource, $secondResource, $thirdResource));

        $this->get($requestConfiguration, $repository)->shouldReturn(array($firstResource, $secondResource, $thirdResource));
    }

    function it_uses_custom_method_and_arguments_if_specified(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments()->willReturn(array('foo'));

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $repository->findAll('foo')->willReturn(array($firstResource));

        $this->get($requestConfiguration, $repository)->shouldReturn(array($firstResource));
    }

    function it_creates_paginator_by_default(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters
    )
    {

        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->getCriteria()->willReturn(array());
        $requestConfiguration->getSorting()->willReturn(array());

        $repository->createPaginator(array(), array())->willReturn($paginator);
       
        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);

        $paginator->setCurrentPage(6)->shouldBeCalled();

        $this->get($requestConfiguration, $repository)->shouldReturn($paginator);
    }

    function it_creates_a_pagniated_respresentation_for_pagerfanta_for_non_html_requests(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters,
        ParameterBag $requestAttributes,
        PagerfantaFactory $pagerfantaRepresentationFactory,
        PaginatedRepresentation $paginatedRepresentation
    )
    {
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->getCriteria()->willReturn(array());
        $requestConfiguration->getSorting()->willReturn(array());

        $repository->createPaginator(array(), array())->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);
        $queryParameters->all()->willReturn(array('foo' => 2, 'bar' => 15));
        $request->attributes = $requestAttributes;
        $requestAttributes->get('_route')->willReturn('sylius_product_index');
        $requestAttributes->get('_route_params')->willReturn(array('slug' => 'foo-bar'));

        $paginator->setCurrentPage(6)->shouldBeCalled();

        $pagerfantaRepresentationFactory->createRepresentation($paginator, Argument::type(Route::class))->willReturn($paginatedRepresentation);

        $this->get($requestConfiguration, $repository)->shouldReturn($paginatedRepresentation);
    }
}
