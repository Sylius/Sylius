<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;

final class DateRange implements CriteriaInterface
{
    private DateTimeProviderInterface $calendar;

    public function __construct(DateTimeProviderInterface $calendar)
    {
        $this->calendar = $calendar;
    }

    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $root = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.startDate IS NULL OR %s.startDate <= :date', $root, $root))
            ->andWhere(sprintf('%s.endDate IS NULL OR %s.endDate >= :date', $root, $root))
            ->setParameter('date', $this->calendar->now())
        ;

        return $queryBuilder;
    }
}
