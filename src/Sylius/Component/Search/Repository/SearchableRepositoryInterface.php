<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Repository;

use Pagerfanta\Pagerfanta;
use Sylius\Component\Search\Model\SearchQueryInterface;

interface SearchableRepositoryInterface
{
    public function searchWithoutTerms(): Pagerfanta;

    public function searchByTerms(SearchQueryInterface $query): Pagerfanta;
}
