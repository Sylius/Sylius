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
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class Enabled implements CriteriaInterface
{
    public function filterQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $root = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere($root . '.enabled = :enabled')
            ->setParameter('enabled', true)
        ;

        return $queryBuilder;
    }

    public function verify(CatalogPromotionInterface $catalogPromotion): bool
    {
        return $catalogPromotion->isEnabled();
    }
}
