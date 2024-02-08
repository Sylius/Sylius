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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class UpdateCatalogPromotionStateHandlerSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        RepositoryInterface $catalogPromotionRepository,
    ): void {
        $this->beConstructedWith($catalogPromotionStateProcessor, $catalogPromotionRepository);
    }

    function it_processes_catalog_promotion_that_has_just_been_created(
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($catalogPromotion);

        $catalogPromotionStateProcessor->process($catalogPromotion)->shouldBeCalled();

        $this(new UpdateCatalogPromotionState('WINTER_MUGS_SALE'));
    }

    function it_processes_catalog_promotion_that_has_just_been_updated(
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($catalogPromotion);

        $catalogPromotionStateProcessor->process($catalogPromotion)->shouldBeCalled();

        $this(new UpdateCatalogPromotionState('WINTER_MUGS_SALE'));
    }

    function it_processes_catalog_promotion_that_has_just_been_ended(
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        RepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn($catalogPromotion);

        $catalogPromotionStateProcessor->process($catalogPromotion)->shouldBeCalled();

        $this(new UpdateCatalogPromotionState('WINTER_MUGS_SALE'));
    }

    function it_does_nothing_if_there_is_no_catalog_promotion_with_given_code(
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        RepositoryInterface $catalogPromotionRepository,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'WINTER_MUGS_SALE'])->willReturn(null);
        $catalogPromotionRepository->findAll()->shouldNotBeCalled();

        $catalogPromotionStateProcessor->process(Argument::any())->shouldNotBeCalled();

        $this(new UpdateCatalogPromotionState('WINTER_MUGS_SALE'));
    }
}
