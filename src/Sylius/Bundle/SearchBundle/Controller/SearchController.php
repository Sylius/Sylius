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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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
        $finder = $this->get('sylius_search.finder')
            ->setTargetIndex('product')
            ->setFacetGroup('search_set')
            ->find(new SearchStringQuery(
                    $request,
                    $this->container->getParameter('sylius_search.pre_search_filter.enabled')
                )
            );

        $paginator = $finder->getPaginator();

        $searchConfig = $this->container->getParameter("sylius_search.config");

        $requestBag = ($request->isMethod('GET'))? $request->query:$request->request;

        if ($paginator) {
            $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());
            $paginator->setCurrentPage($requestBag->get('page', 1));
        }

        $view = $this
            ->view()
            ->setTemplate('SyliusSearchBundle::index.html.twig')
            ->setData(array(
                'results' => $paginator,
                'facets' => $finder->getFacets(),
                'facetTags' => $searchConfig['filters']['facets'],
                'filters' => $finder->getFilters(),
                'searchTerm' => $requestBag->get('q'),
                'searchParam' => $requestBag->get('search_param'),
                'requestMethod' => $this->container->getParameter('sylius_search.request.method'),
            ));

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $filters = array();

        if ($this->container->getParameter('sylius_search.pre_search_filter.enabled')) {
            $taxonomy = $this->get('sylius.repository.taxonomy')
                ->findOneBy(
                    array(
                        'name' => strtoupper($this->container->getParameter('sylius_search.pre_search_filter.taxon'))
                    )
                );

            $filters = array();
            if ($taxonomy) {
                foreach ($taxonomy->getTaxons() as $taxon) {
                    $filters[] = $taxon->getName();
                }
            }

        }

        $requestBag = ($request->isMethod('GET'))? $request->query:$request->request;

        $view = $this
            ->view()
            ->setTemplate($this->container->getParameter('sylius_search.search.template'))
            ->setData(array(
                'filters' => $filters,
                'searchTerm' => $requestBag->get('q'),
                'searchParam' => $requestBag->get('search_param'),
                'requestMethod' => $this->container->getParameter('sylius_search.request.method'),
            ));

        return $this->handleView($view);
    }
}
