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

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use DateTime;
use DomainException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionArchivalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionArchivalProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionAlreadyArchivedException;
use Sylius\Component\Promotion\Exception\CatalogPromotionAlreadyRestoredException;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class CatalogPromotionArchivalProcessorSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionArchivalAnnouncerInterface $catalogPromotionArchivalAnnouncer,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, $catalogPromotionArchivalAnnouncer);
    }

    function it_implements_catalog_promotion_clearer_interface(): void
    {
        $this->shouldImplement(CatalogPromotionArchivalProcessorInterface::class);
    }

    function it_archives_given_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionArchivalAnnouncerInterface $catalogPromotionArchivalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(null);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_ACTIVE, CatalogPromotionStates::STATE_INACTIVE);
        $catalogPromotionArchivalAnnouncer->dispatchCatalogPromotionArchival($catalogPromotion)->shouldBeCalled();

        $this->archiveCatalogPromotion($promotionCode);
        $this->archiveCatalogPromotion($promotionCode);
    }

    function it_throws_exception_trying_to_archive_already_archived_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(new DateTime());

        $this->shouldThrow(CatalogPromotionAlreadyArchivedException::class)
            ->during('archiveCatalogPromotion', [$promotionCode]);
    }

    function it_throws_exception_trying_to_archive_catalog_promotion_when_the_promotion_is_not_found(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn(null);

        $this->shouldThrow(CatalogPromotionNotFoundException::class)
            ->during('archiveCatalogPromotion', [$promotionCode]);
    }

    function it_throws_exception_trying_to_archive_catalog_promotion_when_the_promotion_is_in_processing_state(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(null);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $this->shouldThrow(InvalidCatalogPromotionStateException::class)
            ->during('archiveCatalogPromotion', [$promotionCode]);
    }

    function it_throws_exception_trying_to_archive_catalog_promotion_when_the_promotion_is_in_invalid_state(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(null);
        $catalogPromotion->getState()->willReturn('invalid-state');

        $this->shouldThrow(DomainException::class)
            ->during('archiveCatalogPromotion', [$promotionCode]);
    }

    function it_restores_previously_archived_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionArchivalAnnouncerInterface $catalogPromotionArchivalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(new DateTime());
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_INACTIVE);
        $catalogPromotionArchivalAnnouncer->dispatchCatalogPromotionRestoral($catalogPromotion)->shouldBeCalledOnce();

        $this->restoreCatalogPromotion($promotionCode);
    }

    function it_throws_exception_trying_to_restore_already_restored_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(null);

        $this->shouldThrow(CatalogPromotionAlreadyRestoredException::class)
            ->during('restoreCatalogPromotion', [$promotionCode]);
    }

    function it_throws_exception_trying_to_restore_catalog_promotion_when_the_promotion_is_not_found(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn(null);

        $this->shouldThrow(CatalogPromotionNotFoundException::class)
            ->during('restoreCatalogPromotion', [$promotionCode]);
    }

    function it_throws_exception_trying_to_restore_catalog_promotion_when_the_promotion_is_in_processing_state(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(new DateTime());
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $this->shouldThrow(InvalidCatalogPromotionStateException::class)
            ->during('restoreCatalogPromotion', [$promotionCode]);
    }

    function it_throws_exception_trying_to_restore_catalog_promotion_when_the_promotion_is_in_invalid_state(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $promotionCode = 'CATALOG_PROMOTION_CODE';
        $catalogPromotionRepository->findOneBy(['code' => $promotionCode])->willReturn($catalogPromotion);

        $catalogPromotion->getArchivedAt()->willReturn(new DateTime());
        $catalogPromotion->getState()->willReturn('invalid-state');

        $this->shouldThrow(DomainException::class)
            ->during('restoreCatalogPromotion', [$promotionCode]);
    }
}
