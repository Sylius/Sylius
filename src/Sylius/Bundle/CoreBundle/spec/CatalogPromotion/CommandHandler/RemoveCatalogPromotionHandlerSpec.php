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

use Doctrine\ORM\EntityManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class RemoveCatalogPromotionHandlerSpec extends ObjectBehavior
{
    public function let(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        EntityManager $entityManager,
    ): void {
        $this->beConstructedWith($catalogPromotionRepository, $entityManager);
    }

    public function it_removes_catalog_promotion_being_processed(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        EntityManager $entityManager,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $this(new RemoveCatalogPromotion('CATALOG_PROMOTION_CODE'));

        $entityManager->remove($catalogPromotion)->shouldBeCalled();
    }

    public function it_throws_an_exception_if_catalog_promotion_is_not_in_a_processing_state(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        EntityManager $entityManager,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn($catalogPromotion);

        $catalogPromotion->getState()->willReturn(CatalogPromotionStates::STATE_ACTIVE);
        $catalogPromotion->getCode()->willReturn('CATALOG_PROMOTION_CODE');

        $entityManager->remove(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(InvalidCatalogPromotionStateException::class)
            ->during('__invoke', [new RemoveCatalogPromotion('CATALOG_PROMOTION_CODE')])
        ;
    }

    public function it_returns_if_there_is_no_catalog_promotion_with_given_code(
        CatalogPromotionRepositoryInterface $catalogPromotionRepository,
        EntityManager $entityManager,
    ): void {
        $catalogPromotionRepository->findOneBy(['code' => 'CATALOG_PROMOTION_CODE'])->willReturn(null);

        $entityManager->remove(Argument::any())->shouldNotBeCalled();

        $this(new RemoveCatalogPromotion('CATALOG_PROMOTION_CODE'));
    }
}
