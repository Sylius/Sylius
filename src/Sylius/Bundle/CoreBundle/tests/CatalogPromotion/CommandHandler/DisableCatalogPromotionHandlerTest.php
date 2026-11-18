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

use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\DisableCatalogPromotion;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\DisableCatalogPromotionHandler;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\TargetedCatalogPromotionProcessorInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionRepositoryInterface;

final class DisableCatalogPromotionHandlerTest extends TestCase
{
    private CatalogPromotionRepositoryInterface&MockObject $catalogPromotionRepository;

    private AllProductVariantsCatalogPromotionsProcessorInterface&MockObject $catalogPromotionsProcessor;

    private MockObject&TargetedCatalogPromotionProcessorInterface $targetedProcessor;

    private DisableCatalogPromotionHandler $handler;

    protected function setUp(): void
    {
        $this->catalogPromotionRepository = $this->createMock(CatalogPromotionRepositoryInterface::class);
        $this->catalogPromotionsProcessor = $this->createMock(AllProductVariantsCatalogPromotionsProcessorInterface::class);
        $this->targetedProcessor = $this->createMock(TargetedCatalogPromotionProcessorInterface::class);

        $this->handler = new DisableCatalogPromotionHandler(
            $this->catalogPromotionRepository,
            $this->catalogPromotionsProcessor,
            $this->targetedProcessor,
        );
    }

    public function testDisablesCatalogPromotion(): void
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
            ->method('disable')
        ;

        $this->targetedProcessor
            ->expects($this->once())
            ->method('process')
            ->with($catalogPromotion)
        ;

        $this->catalogPromotionsProcessor
            ->expects($this->never())
            ->method('process')
        ;

        ($this->handler)(new DisableCatalogPromotion($promotionCode));
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

        $this->targetedProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $this->catalogPromotionsProcessor
            ->expects($this->never())
            ->method('process')
        ;

        ($this->handler)(new DisableCatalogPromotion($promotionCode));
    }

    #[IgnoreDeprecations]
    public function testDisablesCatalogPromotionAndProcessesAllVariantsAndTriggersDeprecationIfNoTargetedProcessorIsGiven(): void
    {
        $this->expectUserDeprecationMessage(sprintf(
            'Since sylius/core-bundle 2.4: Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
            TargetedCatalogPromotionProcessorInterface::class,
            DisableCatalogPromotionHandler::class,
        ));

        $handler = new DisableCatalogPromotionHandler(
            $this->catalogPromotionRepository,
            $this->catalogPromotionsProcessor,
        );

        $catalogPromotion = $this->createMock(CatalogPromotionInterface::class);

        $this->catalogPromotionRepository
            ->method('findOneBy')
            ->with(['code' => 'CATALOG_PROMOTION_CODE'])
            ->willReturn($catalogPromotion)
        ;

        $catalogPromotion
            ->expects($this->once())
            ->method('disable')
        ;

        $this->catalogPromotionsProcessor
            ->expects($this->once())
            ->method('process')
        ;

        $this->targetedProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $handler(new DisableCatalogPromotion('CATALOG_PROMOTION_CODE'));
    }
}
