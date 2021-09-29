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
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class AllCatalogPromotionsProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository
    ): void {
        $this->beConstructedWith(
            $catalogPromotionClearer,
            $catalogPromotionProcessor,
            $catalogPromotionRepository
        );
    }

    function it_clears_and_processes_catalog_promotions(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion
    ): void {
        $catalogPromotionClearer->clear()->shouldBeCalled();

        $catalogPromotionRepository->findAll()->willReturn([$firstCatalogPromotion, $secondCatalogPromotion]);

        $catalogPromotionProcessor->process($firstCatalogPromotion)->shouldBeCalled();
        $catalogPromotionProcessor->process($secondCatalogPromotion)->shouldBeCalled();

        $this->process();
    }
}
