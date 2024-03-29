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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\QueryCollectionExtension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;

final class HideArchivedPromotionExtensionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('promotionClass');
    }

    function it_does_nothing_if_current_resource_is_not_a_promotion(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, 'taxonClass', 'get', []);
    }

    function it_does_nothing_if_archived_at_filter_is_already_applied(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere()->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, 'promotionClass', 'get', ['filters' => ['exists' => ['archivedAt' => 'true']]]);
    }

    function it_filters_archived_promotions(
        QueryBuilder $queryBuilder,
        Expr $expr,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->willReturn(['o']);

        $expr->isNull('o.archivedAt')->willReturn('o.archivedAt IS NULL');
        $queryBuilder->expr()->willReturn($expr);
        $queryBuilder->andWhere('o.archivedAt IS NULL')->shouldBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, 'promotionClass', 'get', []);
    }
}
