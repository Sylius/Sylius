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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\RemoveCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\RemoveCatalogPromotionHandler;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class RemoveCatalogPromotionHandlerTest extends TestCase
{
    private CatalogPromotionRepositoryInterface&MockObject $catalogPromotionRepository;

    private EntityManagerInterface&MockObject $entityManager;

    private RemoveCatalogPromotionHandler $handler;

    protected function setUp(): void
    {
        $this->catalogPromotionRepository = $this->createMock(CatalogPromotionRepositoryInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->handler = new RemoveCatalogPromotionHandler(
            $this->catalogPromotionRepository,
            $this->entityManager,
        );
    }

    public function testRemovesCatalogPromotionBeingProcessed(): void
    {
        $promotionCode = 'CATALOG_PROMOTION_CODE';

        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $promotionCode])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion
            ->expects($this->once())
            ->method('getState')
            ->willReturn(CatalogPromotionStates::STATE_PROCESSING)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($catalogPromotion)
        ;

        ($this->handler)(new RemoveCatalogPromotion($promotionCode));
    }

    public function testThrowsAnExceptionIfCatalogPromotionIsNotInAProcessingState(): void
    {
        $promotionCode = 'CATALOG_PROMOTION_CODE';

        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $promotionCode])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion
            ->expects($this->once())
            ->method('getState')
            ->willReturn(CatalogPromotionStates::STATE_ACTIVE)
        ;

        $catalogPromotion
            ->expects($this->once())
            ->method('getCode')
            ->willReturn($promotionCode)
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('remove')
        ;

        $this->expectException(InvalidCatalogPromotionStateException::class);
        $this->expectExceptionMessage(sprintf(
            'Catalog promotion with code "%s" cannot be removed as it is not in a processing state.',
            $promotionCode,
        ));

        ($this->handler)(new RemoveCatalogPromotion($promotionCode));
    }

    public function testReturnsIfThereIsNoCatalogPromotionWithGivenCode(): void
    {
        $promotionCode = 'CATALOG_PROMOTION_CODE';

        $this->catalogPromotionRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['code' => $promotionCode])
            ->willReturn(null)
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('remove')
        ;

        ($this->handler)(new RemoveCatalogPromotion($promotionCode));
    }
}
