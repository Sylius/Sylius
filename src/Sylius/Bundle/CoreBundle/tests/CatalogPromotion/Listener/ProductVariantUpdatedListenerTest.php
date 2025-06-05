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

namespace Tests\Sylius\Bundle\CoreBundle\CatalogPromotion\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantUpdatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductVariantUpdated;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class ProductVariantUpdatedListenerTest extends TestCase
{
    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private MockObject&ProductVariantCatalogPromotionsProcessorInterface $productVariantCatalogPromotionsProcessor;

    private EntityManagerInterface&MockObject $entityManager;

    private ProductVariantUpdatedListener $listener;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->productVariantCatalogPromotionsProcessor = $this->createMock(ProductVariantCatalogPromotionsProcessorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new ProductVariantUpdatedListener(
            $this->productVariantRepository,
            $this->productVariantCatalogPromotionsProcessor,
            $this->entityManager,
        );
    }

    public function testProcessesCatalogPromotionsForUpdatedProductVariant(): void
    {
        $variant = $this->createMock(ProductVariantInterface::class);

        $this->productVariantRepository
            ->method('findOneBy')
            ->with(['code' => 'PHP_MUG'])
            ->willReturn($variant)
        ;

        $this->productVariantCatalogPromotionsProcessor
            ->expects($this->once())
            ->method('process')
            ->with($variant)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        ($this->listener)(new ProductVariantUpdated('PHP_MUG'));
    }

    public function testDoesNothingIfThereIsNoProductVariantWithGivenCode(): void
    {
        $this->productVariantRepository
            ->method('findOneBy')
            ->with(['code' => 'PHP_MUG'])
            ->willReturn(null)
        ;

        $this->productVariantCatalogPromotionsProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        ($this->listener)(new ProductVariantUpdated('PHP_MUG'));
    }
}
