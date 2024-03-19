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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class CatalogPromotionRemovalProcessorSpec extends ObjectBehavior
{
    public function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, $catalogPromotionRemovalAnnouncer);
    }

    public function it_implements_catalog_promotion_removal_processor_interface(): void
    {
        $this->shouldImplement(CatalogPromotionRemovalProcessorInterface::class);
    }

    public function it_removes_an_active_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_ACTIVE);

        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion)->shouldBeCalled();

        $this->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function it_removes_an_inactive_catalog_promotion(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_INACTIVE);

        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval($catalogPromotion)->shouldBeCalled();

        $this->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function it_does_not_dispatch_catalog_promotion_removal_if_catalog_promotion_from_command_does_not_exist(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn(null);
        $catalogPromotion->getState()->shouldNotBeCalled();

        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(CatalogPromotionNotFoundException::class)
            ->during('removeCatalogPromotion', ['CATALOG_PROMOTION_CODE'])
        ;
    }

    public function it_throws_an_exception_if_catalog_promotion_is_being_processed(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);
        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(InvalidCatalogPromotionStateException::class)
            ->during('removeCatalogPromotion', ['CATALOG_PROMOTION_CODE'])
        ;
    }

    public function it_throws_an_exception_if_catalog_promotion_state_is_out_of_invalid_one(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        CatalogPromotionRemovalAnnouncerInterface $catalogPromotionRemovalAnnouncer,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);
        $catalogPromotion->getState()->willReturn('invalid_state');

        $catalogPromotionRemovalAnnouncer->dispatchCatalogPromotionRemoval(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\DomainException::class)
            ->during('removeCatalogPromotion', ['CATALOG_PROMOTION_CODE'])
        ;
    }
}
