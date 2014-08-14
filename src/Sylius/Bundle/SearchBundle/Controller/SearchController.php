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
use Sylius\Bundle\SearchBundle\Entity\SearchLog;
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
                    $request->query->get('q'),
                    $request->query->get('search_param'),
                    $request->query->get('filters'),
                    $this->container->getParameter('sylius_search.pre_search_filter.enabled')
                )
            );

        $selectedFilters = array();
        if ($request->query->has('filters')) {
            $selectedFilters = $request->query->get('filters');
        }

        $paginator = $finder->getPaginator();
        $facets = $finder->getFacets();

        $searchConfig = $this->container->getParameter("sylius_search.config");

        if (isset($paginator)) {
            $paginator->setMaxPerPage($this->config->getPaginationMaxPerPage());
            $paginator->setCurrentPage($request->query->get('page', 1));
        }

        $this->logSearchString(
            $request->query->get('q'),
            $request->headers->get('User-Agent'),
            $request->getClientIp()
        );

        $view = $this
            ->view()
            ->setTemplate('SyliusSearchBundle::index.html.twig')
            ->setData(array(
                'results' => $paginator,
                'facets' => $facets,
                'selectedFilters' => $selectedFilters,
                'facetTags' => $searchConfig['filters']['facets'],
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
            if (!empty($taxonomy)) {
                foreach ($taxonomy->getTaxons() as $taxon) {
                    $filters[] = $taxon->getName();
                }
            }

        }

        $view = $this
            ->view()
            ->setTemplate($this->container->getParameter('sylius_search.search.template'))
            ->setData(array(
                'filters' => $filters,
                'searchTerm' => $request->query->get('q'),
                'searchParam' => $request->query->get('search_param'),
            ));

        return $this->handleView($view);
    }

    /**
     * Logs a search
     *
     * TODO: I could move this to a listener hooked in a find event
     *
     * @param $searchString
     * @param $userAgent
     * @param $remoteAddress
     *
     * @internal param $request
     */
    private function logSearchString($searchString, $userAgent, $remoteAddress)
    {
        $searchLog = new SearchLog();

        $searchLog->setSearchString($searchString);
        $searchLog->setClient($userAgent);
        $searchLog->setRemoteAddress($remoteAddress);

        $em = $this->getDoctrine()->getManager();
        $em->persist($searchLog);
        $em->flush();
    }
}
