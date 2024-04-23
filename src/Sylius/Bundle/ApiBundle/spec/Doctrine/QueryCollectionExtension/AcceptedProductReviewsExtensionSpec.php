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

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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

        $queryBuilder->andWhere(Argument::any())->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::cetera())->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class, new Get(name: 'shop_get'));
    }

    function it_does_nothing_if_operation_name_is_not_shop_get(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryBuilder->getRootAliases()->shouldNotBeCalled();

        $queryBuilder->andWhere(Argument::any())->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::cetera())->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductReview::class, new Get(name: 'admin_get'));
    }

    function it_filters_accepted_product_reviews(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $queryNameGenerator->generateParameterName('status')->shouldBeCalled()->willReturn('status');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.status = :status')->willReturn($queryBuilder);
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductReview::class, new Get(name: 'shop_get'));
    }
}
