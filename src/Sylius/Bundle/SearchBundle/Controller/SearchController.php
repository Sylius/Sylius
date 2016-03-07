<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SearchBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Search landing page controller.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchController extends ResourceController
{
    /**
     * Search landing page.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        /*
         * when using elastic search if you want to setup multiple indexes and control
         * them separately you can do so by adding the index service with a setter
         *
         * ->setTargetIndex($this->get('fos_elastica.index.my_own_index'))
         *
         * where my_own_index is the index name used in the configuration
         * fos_elastica:
         *      indexes:
         *          my_own_index:
         */
        $finder = $this->container->get('sylius_search.finder')
            ->addTargetType('product')
            ->setFacetGroup('search_set')
            ->find(new SearchStringQuery(
                    $request,
                    $this->container->getParameter('sylius_search.pre_search_filter.enabled')
                )
            );

        $paginator = $finder->getPaginator();

        $searchConfig = $this->container->getParameter('sylius_search.config');

        if ($paginator) {
            $paginator->setMaxPerPage($configuration->getPaginationMaxPerPage());
            $paginator->setCurrentPage($this->container->get('sylius_search.request_handler')->getPage());
        }

        $view = View::create()
            ->setTemplate('SyliusSearchBundle::index.html.twig')
            ->setData([
                'results' => $paginator,
                'facets' => $finder->getFacets(),
                'facetTags' => $searchConfig['filters']['facets'],
                'filters' => $finder->getFilters(),
                'searchTerm' => $this->container->get('sylius_search.request_handler')->getQuery(),
                'searchParam' => $this->container->get('sylius_search.request_handler')->getSearchParam(),
                'requestMethod' => $this->container->getParameter('sylius_search.request.method'),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $filters = [];

        if ($this->container->getParameter('sylius_search.pre_search_filter.enabled')) {
            $rootTaxon = $this->get('sylius.repository.taxon')->findOneBy([
                'code' => $this->container->getParameter('sylius_search.pre_search_filter.taxon'),
            ]);

            $filters = [];
            if ($rootTaxon) {
                /** @var TaxonInterface $rootTaxon */
                foreach ($rootTaxon->getChildren() as $taxon) {
                    $filters[] = $taxon->getName();
                }
            }
        }

        $this->container->get('sylius_search.request_handler')->setRequest($request);

        $view = View::create()
            ->setTemplate($this->container->getParameter('sylius_search.search.template'))
            ->setData([
                'filters' => $filters,
                'searchTerm' => $this->container->get('sylius_search.request_handler')->getQuery(),
                'searchParam' => $this->container->get('sylius_search.request_handler')->getSearchParam(),
                'requestMethod' => $this->container->getParameter('sylius_search.request.method'),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }
}
