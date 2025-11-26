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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Processor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Announcer\CatalogPromotionRemovalAnnouncerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Exception\CatalogPromotionNotFoundException;
use Sylius\Component\Promotion\Exception\InvalidCatalogPromotionStateException;
use Sylius\Component\Promotion\Model\CatalogPromotionStates;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class CatalogPromotionRemovalProcessorTest extends TestCase
{
    private CatalogPromotionRepositoryInterface&MockObject $catalogPromotionRepository;

    private CatalogPromotionRemovalAnnouncerInterface&MockObject $catalogPromotionRemovalAnnouncer;

    private CatalogPromotionRemovalProcessorInterface $processor;

    protected function setUp(): void
    {
        $this->catalogPromotionRepository = $this->createMock(CatalogPromotionRepositoryInterface::class);
        $this->catalogPromotionRemovalAnnouncer = $this->createMock(CatalogPromotionRemovalAnnouncerInterface::class);
        $this->processor = new CatalogPromotionRemovalProcessor(
            $this->catalogPromotionRepository,
            $this->catalogPromotionRemovalAnnouncer,
        );
    }

    public function testImplementsCatalogPromotionRemovalProcessorInterface(): void
    {
        $this->assertInstanceOf(CatalogPromotionRemovalProcessorInterface::class, $this->processor);
    }

    public function testRemovesActiveCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'CATALOG_PROMOTION_CODE'])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion->method('getState')->willReturn(CatalogPromotionStates::STATE_ACTIVE);

        $this->catalogPromotionRemovalAnnouncer
            ->expects($this->once())
            ->method('dispatchCatalogPromotionRemoval')
            ->with($catalogPromotion)
        ;

        $this->processor->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function testRemovesInactiveCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'CATALOG_PROMOTION_CODE'])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion->method('getState')->willReturn(CatalogPromotionStates::STATE_INACTIVE);

        $this->catalogPromotionRemovalAnnouncer
            ->expects($this->once())
            ->method('dispatchCatalogPromotionRemoval')
            ->with($catalogPromotion)
        ;

        $this->processor->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function testThrowsExceptionIfCatalogPromotionDoesNotExist(): void
    {
        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'CATALOG_PROMOTION_CODE'])
            ->willReturn(null)
        ;

        $this->catalogPromotionRemovalAnnouncer
            ->expects($this->never())
            ->method('dispatchCatalogPromotionRemoval')
        ;

        $this->expectException(CatalogPromotionNotFoundException::class);

        $this->processor->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function testThrowsExceptionIfCatalogPromotionIsBeingProcessed(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'CATALOG_PROMOTION_CODE'])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion->method('getState')->willReturn(CatalogPromotionStates::STATE_PROCESSING);

        $this->catalogPromotionRemovalAnnouncer
            ->expects($this->never())
            ->method('dispatchCatalogPromotionRemoval')
        ;

        $this->expectException(InvalidCatalogPromotionStateException::class);

        $this->processor->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }

    public function testThrowsExceptionIfCatalogPromotionStateIsInvalid(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'CATALOG_PROMOTION_CODE'])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion->method('getState')->willReturn('invalid_state');

        $this->catalogPromotionRemovalAnnouncer
            ->expects($this->never())
            ->method('dispatchCatalogPromotionRemoval')
        ;

        $this->expectException(\DomainException::class);

        $this->processor->removeCatalogPromotion('CATALOG_PROMOTION_CODE');
    }
}
