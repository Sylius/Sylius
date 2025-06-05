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
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductCreatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Component\Core\Event\ProductCreated;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

final class ProductCreatedListenerTest extends TestCase
{
    private MockObject&ProductRepositoryInterface $productRepository;

    private MockObject&ProductCatalogPromotionsProcessorInterface $productCatalogPromotionsProcessor;

    private EntityManagerInterface&MockObject $entityManager;

    private ProductCreatedListener $listener;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->productCatalogPromotionsProcessor = $this->createMock(ProductCatalogPromotionsProcessorInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new ProductCreatedListener(
            $this->productRepository,
            $this->productCatalogPromotionsProcessor,
            $this->entityManager,
        );
    }

    public function testProcessesCatalogPromotionsForCreatedProduct(): void
    {
        $product = $this->createMock(ProductInterface::class);
        $this->productRepository
            ->method('findOneBy')
            ->with(['code' => 'MUG'])
            ->willReturn($product)
        ;

        $this->productCatalogPromotionsProcessor
            ->expects($this->once())
            ->method('process')
            ->with($product)
        ;

        $this->entityManager
            ->expects($this->once())
            ->method('flush')
        ;

        ($this->listener)(new ProductCreated('MUG'));
    }

    public function testDoesNothingIfThereIsNoProductWithGivenCode(): void
    {
        $this->productRepository
            ->method('findOneBy')
            ->with(['code' => 'MUG'])
            ->willReturn(null)
        ;

        $this->productCatalogPromotionsProcessor
            ->expects($this->never())
            ->method('process')
        ;

        $this->entityManager
            ->expects($this->never())
            ->method('flush')
        ;

        ($this->listener)(new ProductCreated('MUG'));
    }
}
