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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\UpdateCatalogPromotionState;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\UpdateCatalogPromotionStateHandler;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

final class UpdateCatalogPromotionStateHandlerTest extends TestCase
{
    private MockObject&RepositoryInterface $catalogPromotionRepository;

    private CatalogPromotionStateProcessorInterface&MockObject $catalogPromotionStateProcessor;

    private UpdateCatalogPromotionStateHandler $handler;

    protected function setUp(): void
    {
        $this->catalogPromotionRepository = $this->createMock(RepositoryInterface::class);
        $this->catalogPromotionStateProcessor = $this->createMock(CatalogPromotionStateProcessorInterface::class);
        $this->handler = new UpdateCatalogPromotionStateHandler(
            $this->catalogPromotionStateProcessor,
            $this->catalogPromotionRepository,
        );
    }

    public function testProcessesCatalogPromotion(): void
    {
        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'WINTER_MUGS_SALE'])
            ->willReturn($catalogPromotion)
        ;

        $this->catalogPromotionStateProcessor
            ->expects($this->once())
            ->method('process')
            ->with($catalogPromotion)
        ;

        $this->handler->__invoke(new UpdateCatalogPromotionState('WINTER_MUGS_SALE'));
    }

    public function testDoesNothingIfThereIsNoCatalogPromotionWithGivenCode(): void
    {
        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'WINTER_MUGS_SALE'])
            ->willReturn(null)
        ;

        $this->catalogPromotionStateProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $this->handler->__invoke(new UpdateCatalogPromotionState('WINTER_MUGS_SALE'));
    }
}
