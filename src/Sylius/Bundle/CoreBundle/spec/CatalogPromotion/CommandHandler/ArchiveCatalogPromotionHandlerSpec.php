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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ArchiveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class ArchiveCatalogPromotionHandlerSpec extends ObjectBehavior
{
    public function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, $catalogPromotionStateProcessor);
    }

    public function it_archives_catalog_promotion_being_inactive(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionStateProcessorInterface $catalogPromotionStateProcessor,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $catalogPromotion->setArchivedAt(Argument::type(\DateTimeImmutable::class))->shouldBeCalledOnce();
        $catalogPromotionStateProcessor->process($catalogPromotion)->shouldBeCalledOnce();

        $this(new ArchiveCatalogPromotion('CATALOG_PROMOTION_CODE'));
    }

    public function it_throws_an_exception_if_catalog_promotion_is_not_in_a_processing_state(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_ACTIVE);
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');

        $this
            ->shouldThrow(InvalidCatalogPromotionStateException::class)
            ->during('__invoke', [new ArchiveCatalogPromotion('CATALOG_PROMOTION_CODE')])
        ;
    }

    public function it_returns_if_there_is_no_catalog_promotion_with_given_code(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn(null);

        $this(new ArchiveCatalogPromotion('CATALOG_PROMOTION_CODE'));
    }
}
