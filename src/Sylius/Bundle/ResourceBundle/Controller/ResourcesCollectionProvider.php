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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ResourcesCollectionProvider implements ResourcesCollectionProviderInterface
{
    /**
     * @var ResourcesResolverInterface
     */
    private $resourcesResolver;

    /**
     * @var PagerfantaFactory
     */
    private $pagerfantaRepresentationFactory;

    /**
     * @param ResourcesResolverInterface $resourcesResolver
     * @param PagerfantaFactory $pagerfantaRepresentationFactory
     */
    public function __construct(ResourcesResolverInterface $resourcesResolver, PagerfantaFactory $pagerfantaRepresentationFactory)
    {
        $this->resourcesResolver = $resourcesResolver;
        $this->pagerfantaRepresentationFactory = $pagerfantaRepresentationFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get(RequestConfiguration $requestConfiguration, RepositoryInterface $repository)
    {
        $resources = $this->resourcesResolver->getResources($requestConfiguration, $repository);

        if ($resources instanceof Pagerfanta) {
            $request = $requestConfiguration->getRequest();
            $resources->setMaxPerPage($requestConfiguration->getPaginationMaxPerPage());
            $resources->setCurrentPage($request->query->get('page', 1));

            if (!$requestConfiguration->isHtmlRequest()) {
                $route = new Route($request->attributes->get('_route'), array_merge($request->attributes->get('_route_params'), $request->query->all()));

                return $this->pagerfantaRepresentationFactory->createRepresentation($resources, $route);
            }
        }

        return $resources;
    }
}
