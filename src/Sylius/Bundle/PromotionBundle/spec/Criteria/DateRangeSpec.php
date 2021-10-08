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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Provider\DateTimeProviderInterface;

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

    function it_filters_out_catalog_promotions_which_has_not_started_yet_or_already_ended(
        DateTimeProviderInterface $calendar,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
        CatalogPromotionInterface $thirdCatalogPromotion
    ): void {
        $calendar->now()->willReturn(new \DateTime());

        $firstCatalogPromotion->getStartDate()->willReturn(new \DateTime('-1 day'));
        $firstCatalogPromotion->getEndDate()->willReturn(new \DateTime('+1 day'));

        $secondCatalogPromotion->getStartDate()->willReturn(new \DateTime('-10 days'));
        $secondCatalogPromotion->getEndDate()->willReturn(new \DateTime('-3 days'));

        $thirdCatalogPromotion->getStartDate()->willReturn(new \DateTime('+1 day'));
        $thirdCatalogPromotion->getEndDate()->willReturn(new \DateTime('+5 days'));

        $this
            ->meets([$firstCatalogPromotion, $secondCatalogPromotion, $thirdCatalogPromotion])
            ->shouldReturn([$firstCatalogPromotion])
        ;
    }
}
