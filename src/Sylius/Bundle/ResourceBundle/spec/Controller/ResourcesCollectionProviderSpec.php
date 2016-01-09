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
use Sylius\Component\Grid\Definition\Grid;
use Sylius\Component\Grid\Parameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Grid\View\GridView;
use Sylius\Component\Grid\View\GridViewFactoryInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * @mixin ResourcesCollectionProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesCollectionProviderSpec extends ObjectBehavior
{
    function let(PagerfantaFactory $pagerfantaRepresentationFactory, GridProviderInterface $gridProvider, GridViewFactoryInterface $gridViewFactory)
    {
        $this->beConstructedWith($pagerfantaRepresentationFactory, $gridProvider, $gridViewFactory);
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
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(false);

        $repository->findAll()->willReturn([$firstResource, $secondResource]);

        $this->get($requestConfiguration, $repository)->shouldReturn([$firstResource, $secondResource]);
    }

    function it_finds_resources_by_criteria_if_not_paginated(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource,
        ResourceInterface $secondResource,
        ResourceInterface $thirdResource
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod(null)->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $requestConfiguration->getCriteria()->willReturn(['custom' => 'criteria']);
        $requestConfiguration->getSorting()->willReturn(['name' => 'desc']);

        $repository->findBy(['custom' => 'criteria'], ['name' => 'desc'], 15)->willReturn([$firstResource, $secondResource, $thirdResource]);

        $this->get($requestConfiguration, $repository)->shouldReturn([$firstResource, $secondResource, $thirdResource]);
    }

    function it_uses_custom_method_and_arguments_if_specified(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        ResourceInterface $firstResource
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments()->willReturn(['foo']);

        $requestConfiguration->isPaginated()->willReturn(false);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $repository->findAll('foo')->willReturn([$firstResource]);

        $this->get($requestConfiguration, $repository)->shouldReturn([$firstResource]);
    }

    function it_creates_paginator_by_default(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->getCriteria()->willReturn([]);
        $requestConfiguration->getSorting()->willReturn([]);

        $repository->createPaginator([], [])->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();

        $this->get($requestConfiguration, $repository)->shouldReturn($paginator);
    }

    function it_creates_a_paginated_representation_for_pagerfanta_for_non_html_requests(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters,
        ParameterBag $requestAttributes,
        PagerfantaFactory $pagerfantaRepresentationFactory,
        PaginatedRepresentation $paginatedRepresentation
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getRepositoryMethod()->willReturn(null);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->getCriteria()->willReturn([]);
        $requestConfiguration->getSorting()->willReturn([]);

        $repository->createPaginator([], [])->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);
        $queryParameters->all()->willReturn(['foo' => 2, 'bar' => 15]);
        $request->attributes = $requestAttributes;
        $requestAttributes->get('_route')->willReturn('sylius_product_index');
        $requestAttributes->get('_route_params')->willReturn(['slug' => 'foo-bar']);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();

        $pagerfantaRepresentationFactory->createRepresentation($paginator, Argument::type(Route::class))->willReturn($paginatedRepresentation);

        $this->get($requestConfiguration, $repository)->shouldReturn($paginatedRepresentation);
    }

    function it_sets_current_page_on_paginator_from_custom_method(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(true);
        $requestConfiguration->getRepositoryMethod()->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments()->willReturn(['foo']);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);
        $requestConfiguration->isLimited()->willReturn(true);
        $requestConfiguration->getLimit()->willReturn(15);

        $repository->findAll('foo')->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(8);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(8)->shouldBeCalled();

        $this->get($requestConfiguration, $repository)->shouldReturn($paginator);
    }

    function it_creates_a_paginated_representation_for_pagerfanta_for_non_html_requests_with_a_custom_repository_method(
        RequestConfiguration $requestConfiguration,
        RepositoryInterface $repository,
        Pagerfanta $paginator,
        Request $request,
        ParameterBag $queryParameters,
        ParameterBag $requestAttributes,
        PagerfantaFactory $pagerfantaRepresentationFactory,
        PaginatedRepresentation $paginatedRepresentation
    ) {
        $requestConfiguration->hasGrid()->willReturn(false);
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getRepositoryMethod()->willReturn('findAll');
        $requestConfiguration->getRepositoryArguments()->willReturn(['foo']);

        $requestConfiguration->isPaginated()->willReturn(true);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);
        $requestConfiguration->isLimited()->willReturn(false);
        $requestConfiguration->getCriteria()->willReturn([]);
        $requestConfiguration->getSorting()->willReturn([]);

        $repository->findAll('foo')->willReturn($paginator);

        $requestConfiguration->getRequest()->willReturn($request);
        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);
        $queryParameters->all()->willReturn(['foo' => 2, 'bar' => 15]);
        $request->attributes = $requestAttributes;
        $requestAttributes->get('_route')->willReturn('sylius_product_index');
        $requestAttributes->get('_route_params')->willReturn(['slug' => 'foo-bar']);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();

        $pagerfantaRepresentationFactory->createRepresentation($paginator, Argument::type(Route::class))->willReturn($paginatedRepresentation);

        $this->get($requestConfiguration, $repository)->shouldReturn($paginatedRepresentation);
    }

    function it_creates_a_grid_view_if_needed(
        Grid $grid,
        GridProviderInterface $gridProvider,
        GridView $gridView,
        GridViewFactoryInterface $gridViewFactory,
        Pagerfanta $paginator,
        PagerfantaFactory $pagerfantaRepresentationFactory,
        PaginatedRepresentation $paginatedRepresentation,
        ParameterBag $queryParameters,
        ParameterBag $requestAttributes,
        RepositoryInterface $repository,
        Request $request,
        RequestConfiguration $requestConfiguration
    )
    {
        $requestConfiguration->hasGrid()->willReturn(true);
        $requestConfiguration->getGrid()->willReturn('sylius_admin_product');
        $requestConfiguration->isHtmlRequest()->willReturn(false);
        $requestConfiguration->getPaginationMaxPerPage()->willReturn(5);

        $requestConfiguration->getRequest()->willReturn($request);

        $request->query = $queryParameters;
        $queryParameters->get('page', 1)->willReturn(6);
        $queryParameters->all()->willReturn(array('foo' => 2, 'bar' => 15));

        $request->attributes = $requestAttributes;
        $requestAttributes->get('_route')->willReturn('sylius_product_index');
        $requestAttributes->get('_route_params')->willReturn(array('slug' => 'foo-bar'));

        $gridProvider->get('sylius_admin_product')->willReturn($grid);
        $gridViewFactory->create($grid, Argument::type(Parameters::class))->willReturn($gridView);
        $gridView->getData()->willReturn($paginator);

        $paginator->setMaxPerPage(5)->shouldBeCalled();
        $paginator->setCurrentPage(6)->shouldBeCalled();

        $pagerfantaRepresentationFactory->createRepresentation($paginator, Argument::type(Route::class))->willReturn($paginatedRepresentation);

        $this->get($requestConfiguration, $repository)->shouldReturn($paginatedRepresentation);
    }
}
