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

namespace spec\Sylius\Bundle\PromotionBundle\Criteria;

use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;

final class DateRangeSpec extends ObjectBehavior
{
    function let(DateTimeProviderInterface $calendar): void
    {
        $this->beConstructedWith($calendar);
    }

    function it_implements_criteria_interface(): void
    {
        $this->shouldImplement(CriteriaInterface::class);
    }

    function it_adds_filters_to_query_builder(DateTimeProviderInterface $calendar, QueryBuilder $queryBuilder): void
    {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $now = new \DateTime();
        $calendar->now()->willReturn($now);

        $queryBuilder->andWhere('o.startDate IS NULL OR o.startDate <= :date')->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->andWhere('o.endDate IS NULL OR o.endDate > :date')->willReturn($queryBuilder)->shouldBeCalled();
        $queryBuilder->setParameter('date', $now)->willReturn($queryBuilder)->shouldBeCalled();

        $this->filterQueryBuilder($queryBuilder)->shouldReturn($queryBuilder);
    }

    function it_verifies_catalog_promotion(
        CatalogPromotionInterface $catalogPromotion,
        DateTimeProviderInterface $calendar
    ): void {
        $tomorrow = new \DateTime('+1day');
        $yesterday = new \DateTime('-1day');
        $now = new \DateTime();
        $calendar->now()->willReturn($now);

        $catalogPromotion->getStartDate()->willReturn($yesterday);
        $catalogPromotion->getEndDate()->willReturn($tomorrow);

        $this->verify($catalogPromotion)->shouldReturn(true);

        $catalogPromotion->getStartDate()->willReturn(null);
        $catalogPromotion->getEndDate()->willReturn(null);

        $this->verify($catalogPromotion)->shouldReturn(true);

        $catalogPromotion->getStartDate()->willReturn($tomorrow);
        $catalogPromotion->getEndDate()->willReturn($yesterday);

        $this->verify($catalogPromotion)->shouldReturn(false);
    }
}
