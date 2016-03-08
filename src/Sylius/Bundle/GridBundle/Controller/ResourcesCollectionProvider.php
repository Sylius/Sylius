<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridViewFactoryInterface;
use Sylius\Component\Grid\Parameters as GridParameters;
use Sylius\Component\Grid\Provider\GridProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesCollectionProvider implements ResourcesCollectionProviderInterface
{
    /**
     * @var PagerfantaFactory
     */
    private $pagerfantaRepresentationFactory;

    /**
     * @var GridProviderInterface
     */
    private $gridProvider;

    /**
     * @var ResourceGridViewFactoryInterface
     */
    private $gridViewFactory;

    /**
     * @param PagerfantaFactory $pagerfantaRepresentationFactory
     * @param GridProviderInterface $gridProvider
     * @param ResourceGridViewFactoryInterface $gridViewFactory
     */
    public function __construct(PagerfantaFactory $pagerfantaRepresentationFactory, GridProviderInterface $gridProvider, ResourceGridViewFactoryInterface $gridViewFactory)
    {
        $this->pagerfantaRepresentationFactory = $pagerfantaRepresentationFactory;
        $this->gridProvider = $gridProvider;
        $this->gridViewFactory = $gridViewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $resources = $this->getResources($requestConfiguration, $repository);

        if ($resources instanceof Pagerfanta) {
            $request = $requestConfiguration->getRequest();
            $resources->setMaxPerPage($requestConfiguration->getPaginationMaxPerPage());
            $resources->setCurrentPage($request->query->get('page', 1));
        }

        if (!$requestConfiguration->isHtmlRequest() && $resources instanceof Pagerfanta) {
            return $this->createPaginatedRepresentation($requestConfiguration, $resources);
        }

        return $resources;
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    private function getResources(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        if ($requestConfiguration->hasGrid()) {
            return $this->getGrid($requestConfiguration);
        }

        if (null !== $repositoryMethod = $requestConfiguration->getRepositoryMethod()) {
            $callable = [$repository, $repositoryMethod];

            $resources = call_user_func_array($callable, $requestConfiguration->getRepositoryArguments());

            return $resources;
        }

        if (!$requestConfiguration->isPaginated() && !$requestConfiguration->isLimited()) {
            return $repository->findAll();
        }

        if (!$requestConfiguration->isPaginated()) {
            return $repository->findBy($requestConfiguration->getCriteria(), $requestConfiguration->getSorting(), $requestConfiguration->getLimit());
        }

        return $repository->createPaginator($requestConfiguration->getCriteria(), $requestConfiguration->getSorting());
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     *
     * @return mixed
     */
    private function getGrid(RequestConfiguration $requestConfiguration)
    {
        $gridDefinition = $this->gridProvider->get($requestConfiguration->getGrid());

        $request = $requestConfiguration->getRequest();
        $parameters = new GridParameters($request->query->all());

        $gridView = $this->gridViewFactory->create($gridDefinition, $parameters, $requestConfiguration->getMetadata(), $requestConfiguration);

        if ($requestConfiguration->isHtmlRequest()) {
            return $gridView;
        }

        return $gridView->getData();
    }

    /**
     * @param RequestConfiguration $requestConfiguration
     * @param Pagerfanta $paginator
     *
     * @return PaginatedRepresentation
     */
    private function createPaginatedRepresentation(RequestConfiguration $requestConfiguration, Pagerfanta $paginator)
    {
        $request = $requestConfiguration->getRequest();
        $route = new Route($request->attributes->get('_route'), array_merge($request->attributes->get('_route_params'), $request->query->all()));

        return $this->pagerfantaRepresentationFactory->createRepresentation($paginator, $route);
    }
}
