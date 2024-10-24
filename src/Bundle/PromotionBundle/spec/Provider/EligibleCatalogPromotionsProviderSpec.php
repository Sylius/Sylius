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

namespace spec\Sylius\Bundle\PromotionBundle\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class EligibleCatalogPromotionsProviderSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, [$firstCriterion, $secondCriterion]);
    }

    function it_implements_eligible_catalog_promotions_provider_interface(): void
    {
        $this->shouldImplement(EligibleCatalogPromotionsProviderInterface::class);
    }

    function it_provides_catalog_promotions_based_on_criteria(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CriteriaInterface $firstCriterion,
        CriteriaInterface $secondCriterion,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
    ): void {
        $catalogPromotionRepository
            ->findByCriteria([$firstCriterion, $secondCriterion])
            ->willReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;

        $this
            ->provide()
            ->shouldReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;
    }
}
