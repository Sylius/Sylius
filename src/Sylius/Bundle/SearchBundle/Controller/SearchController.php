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

use Sylius\Bundle\SearchBundle\Entity\SearchLog;
use Sylius\Bundle\SearchBundle\Query\SearchStringQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Search landing page controller.
 *
 * @author Argyrios Gounaris <agounaris@gmail.com>
 */
class SearchController extends Controller
{

    /**
     * Search landing page.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
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
                    $this->container->getParameter('sylius_search.filter.enabled')
                )
            );

        $selectedFilters = array();
        if (is_array($request->query->get('filters'))) {
            $selectedFilters = $request->query->get('filters');
        }

        $paginator = $finder->getPaginator();
        $facets = $finder->getFacets();

        $config = $this->container->getParameter("sylius_search.config");

        if (isset($paginator)) {
            $paginator->setMaxPerPage($config['items_per_page']);
            $paginator->setCurrentPage($request->query->get('page', 1));
        }

        $this->logSearchString(
            $request->query->get('q'),
            $request->headers->get('User-Agent'),
            $request->getClientIp()
        );

        return $this->render('SyliusSearchBundle::search.html.twig', array(
            'results' => $paginator,
            'facets' => $facets,
            'selectedFilters' => $selectedFilters,
            'facetTags' => $config['filters']['facets'],
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function formAction(Request $request)
    {
        $filters = array();

        if ($this->container->getParameter('sylius_search.filter.enabled')) {
            $taxonomy = $this->get('sylius.repository.taxonomy')
                ->findOneBy(
                    array(
                        'name' => strtoupper($this->container->getParameter('sylius_search.filter.taxonomy'))
                    )
                );

            $filters = array();
            if (!empty($taxonomy)) {
                foreach ($taxonomy->getTaxons() as $taxon) {
                    $filters[] = $taxon->getName();
                }
            }

        }

        return $this->render($this->container->getParameter('sylius_search.form'), array(
            'filters' => $filters,
            'searchTerm' => $request->query->get('q'),
            'searchParam' => $request->query->get('search_param'),
        ));
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
