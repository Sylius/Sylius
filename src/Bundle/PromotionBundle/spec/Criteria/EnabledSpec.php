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

namespace spec\Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class EnabledSpec extends ObjectBehavior
{
    function it_implements_criteria_interface(): void
    {
        $this->shouldImplement(CriteriaInterface::class);
    }

    function it_adds_filters_to_query_builder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->getRootAliases()->willReturn(['catalog_promotion']);

        $queryBuilder->andWhere('catalog_promotion.enabled = :enabled')->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->setParameter('enabled', true)->willReturn($queryBuilder)->shouldBeCalled();

        $this->filterQueryBuilder($queryBuilder)->shouldReturn($queryBuilder);
    }

    function it_verifies_catalog_promotion(CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotion->isEnabled()->willReturn(true, false);

        $this->verify($catalogPromotion)->shouldReturn(true);
        $this->verify($catalogPromotion)->shouldReturn(false);
    }
}
