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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Command\ApplyCatalogPromotionsOnVariants;
use Sylius\Bundle\CoreBundle\CatalogPromotion\CommandHandler\ApplyCatalogPromotionsOnVariantsHandler;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\PromotionBundle\Provider\EligibleCatalogPromotionsProviderInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ApplyCatalogPromotionsOnVariantsHandlerTest extends TestCase
{
    private EligibleCatalogPromotionsProviderInterface&MockObject $catalogPromotionsProvider;

    private CatalogPromotionApplicatorInterface&MockObject $catalogPromotionApplicator;

    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private CatalogPromotionClearerInterface&MockObject $clearer;

    private ApplyCatalogPromotionsOnVariantsHandler $handler;

    protected function setUp(): void
    {
        $this->catalogPromotionsProvider = $this->createMock(EligibleCatalogPromotionsProviderInterface::class);
        $this->catalogPromotionApplicator = $this->createMock(CatalogPromotionApplicatorInterface::class);
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->clearer = $this->createMock(CatalogPromotionClearerInterface::class);

        $this->handler = new ApplyCatalogPromotionsOnVariantsHandler(
            $this->catalogPromotionsProvider,
            $this->catalogPromotionApplicator,
            $this->productVariantRepository,
            $this->clearer,
        );
    }

    public function testAppliesCatalogPromotionOnProvidedVariants(): void
    {
        $variantCodes = ['FIRST_VARIANT', 'SECOND_VARIANT'];

        $firstPromotion = $this->createMock(CatalogPromotionInterface::class);
        $secondPromotion = $this->createMock(CatalogPromotionInterface::class);

        $firstVariant = $this->createMock(ProductVariantInterface::class);
        $secondVariant = $this->createMock(ProductVariantInterface::class);

        $this->catalogPromotionsProvider
            ->expects($this->once())
            ->method('provide')
            ->willReturn([$firstPromotion, $secondPromotion])
        ;

        $this->productVariantRepository
            ->expects($this->once())
            ->method('findByCodes')
            ->with($variantCodes)
            ->willReturn([$firstVariant, $secondVariant])
        ;

        $this->clearer
            ->expects($this->exactly(2))
            ->method('clearVariant')
            ->with($this->logicalOr($firstVariant, $secondVariant))
        ;

        $this->catalogPromotionApplicator
            ->expects($this->exactly(4))
            ->method('applyOnVariant')
            ->with(
                $this->isInstanceOf(ProductVariantInterface::class),
                $this->isInstanceOf(CatalogPromotionInterface::class),
            )
        ;

        ($this->handler)(new ApplyCatalogPromotionsOnVariants($variantCodes));
    }
}
