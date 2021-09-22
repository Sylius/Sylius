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

namespace spec\Sylius\Bundle\CoreBundle\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionUpdated;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CatalogPromotionUpdateListenerSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository
    ): void {
        $this->beConstructedWith($catalogPromotionClearer, $catalogPromotionProcessor, $catalogPromotionRepository);
    }

    function it_processes_catalog_promotion_that_has_just_been_updated(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $firstCatalogPromotion,
        CatalogPromotionInterface $secondCatalogPromotion
    ): void {
        $catalogPromotionClearer->clear()->shouldBeCalled();

        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($firstCatalogPromotion);

        $catalogPromotionRepository->findAll()->willReturn([$firstCatalogPromotion, $secondCatalogPromotion]);

        $catalogPromotionProcessor->process($firstCatalogPromotion)->shouldBeCalled();
        $catalogPromotionProcessor->process($secondCatalogPromotion)->shouldBeCalled();

        $this->__invoke(new CatalogPromotionUpdated('WINTER_MUGS_SALE'));
    }

    function it_does_nothing_if_there_is_not_catalog_promotion_with_given_code(
        CatalogPromotionClearerInterface $catalogPromotionClearer,
        CatalogPromotionProcessorInterface $catalogPromotionProcessor,
        RepositoryInterface $catalogPromotionRepository
    ): void {
        $catalogPromotionClearer->clear()->shouldNotBeCalled();

        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn(null);
        $catalogPromotionRepository->findAll()->shouldNotBeCalled();

        $catalogPromotionProcessor->process(Argument::any())->shouldNotBeCalled();

        $this->__invoke(new CatalogPromotionUpdated('WINTER_MUGS_SALE'));
    }
}
