<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;

interface CriteriaInterface
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;
}
