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
use Sylius\Bundle\ResourceBundle\Controller\ResourcesResolverInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ResourcesCollectionProviderSpec extends ObjectBehavior
{
    function let(ResourcesResolverInterface $resourcesResolver, PagerfantaFactory $pagerfantaRepresentationFactory)
    {
        $this->beConstructedWith($resourcesResolver, $pagerfantaRepresentationFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ResourcesCollectionProvider::class);
    }

    function it_implements_resources_collection_provider_interface()
    {
        $this->shouldImplement(ResourcesCollectionProviderInterface::class);
    }

    function it_returns_resources_resolved_from_repository(
        ResourcesResolverInterface $resourcesResolver,
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource,
        ResourceInterface $secondResource
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);

        $resourcesResolver->getResources($requestConfiguration, $repository)->willReturn([$firstResource, $secondResource]);

        $this->get($requestConfiguration, $repository)->shouldReturn([$firstResource, $secondResource]);
    }

    function it_handles_Pagerfanta(
        ResourcesResolverInterface $resourcesResolver,
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);

        $resourcesResolver->getResources($requestConfiguration, $repository)->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();
        $paginator->getCurrentPageResults()->shouldBeCalled();

        $this->get($requestConfiguration, $repository)->shouldReturn($paginator);
    }

    function it_creates_a_paginated_representation_for_pagerfanta_for_non_html_requests(
        ResourcesResolverInterface $resourcesResolver,
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters,
        ParameterBag $requestAttributes,
        PagerfantaFactory $pagerfantaRepresentationFactory,
        PaginatedRepresentation $paginatedRepresentation
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(8);

        $resourcesResolver->getResources($requestConfiguration, $repository)->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);
        $queryParameters->all()->willReturn(['foo' => 2, 'bar' => 15]);
        $request->attributes = $requestAttributes;
        $requestAttributes->get('_route')->willReturn('sylius_product_index');
        $requestAttributes->get('_route_params')->willReturn(['slug' => 'foo-bar']);

        $paginator->setMaxPerPage(8)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();
        $paginator->getCurrentPageResults()->shouldBeCalled();

        $pagerfantaRepresentationFactory->createRepresentation($paginator, Argument::type(Route::class))->willReturn($paginatedRepresentation);

        $this->get($requestConfiguration, $repository)->shouldReturn($paginatedRepresentation);
    }

    function it_handles_resource_grid_view(
        ResourcesResolverInterface $resourcesResolver,
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceGridView $resourceGridView,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters
    ) {
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);

        $resourcesResolver->getResources($requestConfiguration, $repository)->willReturn($resourceGridView);
        $resourceGridView->getData()->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();
        $paginator->getCurrentPageResults()->shouldBeCalled();

        $this->get($requestConfiguration, $repository)->shouldReturn($resourceGridView);
    }
}
