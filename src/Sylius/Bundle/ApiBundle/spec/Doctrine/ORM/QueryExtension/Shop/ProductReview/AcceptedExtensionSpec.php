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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductReview;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Model\ReviewInterface;

final class AcceptedExtensionSpec extends ObjectBehavior
{
    function let(SectionProviderInterface $sectionProvider): void
    {
        $this->beConstructedWith($sectionProvider);
    }

    function it_does_nothing_if_current_resource_is_not_a_product_review(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->shouldNotBeCalled();

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere(Argument::any())->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::cetera())->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, \stdClass::class);
    }

    function it_does_nothing_when_the_operation_is_outside_shop_api_section(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        SectionInterface $section,
    ): void {
        $sectionProvider->getSection()->willReturn($section);

        $queryBuilder->getRootAliases()->shouldNotBeCalled();
        $queryBuilder->andWhere(Argument::any())->shouldNotBeCalled();
        $queryBuilder->setParameter(Argument::cetera())->shouldNotBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductReview::class);
    }

    function it_filters_accepted_product_reviews(
        SectionProviderInterface $sectionProvider,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $sectionProvider->getSection()->willReturn(new ShopApiSection());

        $queryNameGenerator->generateParameterName('status')->shouldBeCalled()->willReturn('status');

        $queryBuilder->getRootAliases()->willReturn(['o']);
        $queryBuilder->andWhere('o.status = :status')->willReturn($queryBuilder);
        $queryBuilder->setParameter('status', ReviewInterface::STATUS_ACCEPTED)->shouldBeCalled();

        $this->applyToCollection($queryBuilder, $queryNameGenerator, ProductReview::class);
    }
}
