<?php

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\AdminBundle\Provider\SearchEngineInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class SearchController extends AbstractController
{
    /** @var SearchEngineInterface  */
    private $searchEngine;

    public function __construct(SearchEngineInterface $searchEngine)
    {
        $this->searchEngine = $searchEngine;
    }

    public function search(Request $request): Response
    {
        $terms = $request->query->get('terms', '');

        return $this->render('@SyliusAdmin/Search/search.html.twig', [
            'terms' => $terms,
            'results' => $this->searchEngine->search($terms, $request->query),
        ]);
    }
}
