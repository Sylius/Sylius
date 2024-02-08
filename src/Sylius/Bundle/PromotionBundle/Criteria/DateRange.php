<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class DateRange implements CriteriaInterface
{
    public function __construct(private DateTimeProviderInterface $calendar)
    {
    }

    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $root = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.startDate IS NULL OR %s.startDate <= :date', $root, $root))
            ->andWhere(sprintf('%s.endDate IS NULL OR %s.endDate > :date', $root, $root))
            ->setParameter('date', $this->calendar->now())
        ;

        return $queryBuilder;
    }

    public function verify(CatalogPromotionInterface $catalogPromotion): bool
    {
        return
            ($catalogPromotion->getStartDate() === null || $catalogPromotion->getStartDate() <= $this->calendar->now()) &&
            ($catalogPromotion->getEndDate() === null || $catalogPromotion->getEndDate() > $this->calendar->now())
        ;
    }
}
