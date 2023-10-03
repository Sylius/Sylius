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
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Model\ReviewInterface;

final class AcceptedProductReviewsExtensionSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->beConstructedWith(ProductReview::class);
    }

    function it_does_nothing_if_current_resource_is_not_a_product_review(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $queryBuilder->andWhere('o.status = :status')->willReturn($queryBuilder);
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'shop_get', []);
    }

    function it_does_nothing_if_operation_name_is_not_shop_get(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $queryBuilder->andWhere('o.status = :status')->willReturn($queryBuilder);
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductInterface::class, 'admin_get', []);
    }

    function it_filters_accepted_product_reviews(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryNameGenerator->generateParameterName('status')->shouldBeCalled()->willReturn('status');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.status = :status')->willReturn($queryBuilder);
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductReview::class, 'shop_get', []);
    }
}
