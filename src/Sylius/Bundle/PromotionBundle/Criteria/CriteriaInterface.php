<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

interface CriteriaInterface
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder;

    public function verify(CatalogPromotionInterface $catalogPromotion): bool;
}
