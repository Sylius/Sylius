<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Doctrine\ORM\QueryBuilder;

use Doctrine\ORM\QueryBuilder;

class QueryBuilderPagination
{
    /**
     * @throws \InvalidArgumentException
     */
    public static function create(QueryBuilder $queryBuilder, int $page, int $maxResults): void
    {
        if ($page < 1) {
            throw new \InvalidArgumentException('Page number must be positive');
        }

        $startAt = $page === 1 ? 0 : ($page - 1) * $maxResults;

        $queryBuilder
            ->setFirstResult($startAt)
            ->setMaxResults($maxResults)
        ;
    }
}
