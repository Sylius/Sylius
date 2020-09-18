<?php

declare(strict_types=1);

namespace Sylius\Component\Search\Provider;

use Sylius\Component\Search\Model\ResultSet;
use Sylius\Component\Search\Model\ResultSetInterface;
use Sylius\Component\Search\Model\SearchQueryInterface;
use Sylius\Component\Search\Repository\SearchableRepositoryInterface;

final class ResultSetProvider implements ResultSetProviderInterface
{
    /** @var string */
    private $type;

    /** @var SearchableRepositoryInterface */
    private $searchableRepository;

    public function __construct(string $type, SearchableRepositoryInterface $searchableRepository)
    {
        $this->searchableRepository = $searchableRepository;
        $this->type = $type;
    }

    public function getResultSet(SearchQueryInterface $query): ResultSetInterface
    {
        if (strlen($query->getTerms()) < 3) {
            $pager = $this->searchableRepository->searchWithoutTerms();
        } else {
            $pager = $this->searchableRepository->searchByTerms($query);
        }

        $pager->setCurrentPage($query->getPageNumber($this->type));

        return new ResultSet($this->type, $pager);
    }
}
