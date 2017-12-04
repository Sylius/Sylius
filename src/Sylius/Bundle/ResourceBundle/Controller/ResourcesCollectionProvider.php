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

namespace Sylius\Bundle\ResourceBundle\Controller;

use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ResourcesCollectionProvider implements ResourcesCollectionProviderInterface
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
        $paginationLimits = [];

        if ($resources instanceof ResourceGridView) {
            $paginator = $resources->getData();
            $paginationLimits = $resources->getDefinition()->getLimits();
        } else {
            $paginator = $resources;
        }

        if ($paginator instanceof Pagerfanta) {
            $request = $requestConfiguration->getRequest();

            $paginator->setMaxPerPage($this->resolveMaxPerPage(
                $request->query->has('limit') ? $request->query->getInt('limit') : null,
                $requestConfiguration->getPaginationMaxPerPage(),
                $paginationLimits
            ));
            $paginator->setCurrentPage($request->query->get('page', 1));

            // This prevents Pagerfanta from querying database from a template
            $paginator->getCurrentPageResults();

            if (!$requestConfiguration->isHtmlRequest()) {
                $route = new Route($request->attributes->get('_route'), array_merge($request->attributes->get('_route_params'), $request->query->all()));

                return $this->pagerfantaRepresentationFactory->createRepresentation($paginator, $route);
            }
        }

        return $resources;
    }

    /**
     * @param int|null $requestLimit
     * @param int $configurationLimit
     * @param int[] $gridLimits
     *
     * @return int
     */
    private function resolveMaxPerPage(?int $requestLimit, int $configurationLimit, array $gridLimits = []): int
    {
        if (null === $requestLimit) {
            return reset($gridLimits) ?: $configurationLimit;
        }

        if (!empty($gridLimits)) {
            $maxGridLimit = max($gridLimits);

            return $requestLimit > $maxGridLimit ? $maxGridLimit : $requestLimit;
        }

        return $requestLimit;
    }
}
