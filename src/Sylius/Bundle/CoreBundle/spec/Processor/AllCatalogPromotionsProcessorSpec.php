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

namespace spec\Sylius\Bundle\CoreBundle\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Bundle\PromotionBundle\Criteria\CriteriaInterface;
use Sylius\Bundle\PromotionBundle\Criteria\Enabled;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;

final class AllCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        CriteriaInterface $firstCriteria,
        CriteriaInterface $secondCriteria
    ): void {
        $this->beConstructedWith(
            $catalogPromotionClearer,
            $catalogPromotionProcessor,
            $catalogPromotionsProvider,
            [$firstCriteria, $secondCriteria]
        );
    }

    function it_clears_and_processes_catalog_promotions(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        EligibleCatalogPromotionsProviderInterface $catalogPromotionsProvider,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion,
        CriteriaInterface $firstCriteria,
        CriteriaInterface $secondCriteria
    ): void {
        $catalogPromotionClearer->clear()->shouldBeCalled();

        $catalogPromotionsProvider
            ->provide([$firstCriteria, $secondCriteria])
            ->willReturn([$firstCatalogPromotion, $secondCatalogPromotion])
        ;

        $catalogPromotionProcessor->process($firstCatalogPromotion)->shouldBeCalled();
        $catalogPromotionProcessor->process($secondCatalogPromotion)->shouldBeCalled();

        $this->process();
    }
}
