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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Product;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\State\ProviderInterface;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\CollectionProvider;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;

final class CollectionProviderTest extends TestCase
{
    private MockObject&ProviderInterface $decoratedCollectionProvider;

    private MockObject&ProductRepositoryInterface $productRepository;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedCollectionProvider = $this->createMock(ProviderInterface::class);
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->collectionProvider = new CollectionProvider(
            $this->decoratedCollectionProvider,
            $this->productRepository,
        );
    }

    public function testHydratesUninitializedReviewsOfAllProvidedProductsAtOnce(): void
    {
        $operation = new GetCollection(class: Product::class);
        $firstProduct = $this->createProductWithReviews($this->createLazyCollection(initialized: false));
        $secondProduct = $this->createProductWithReviews($this->createLazyCollection(initialized: false));
        $productWithLoadedReviews = $this->createProductWithReviews($this->createLazyCollection(initialized: true));
        $products = [$firstProduct, $secondProduct, $productWithLoadedReviews];

        $this->decoratedCollectionProvider
            ->expects(self::once())
            ->method('provide')
            ->with($operation, ['uriVariable' => 'value'], ['context' => 'value'])
            ->willReturn($products)
        ;

        $this->productRepository
            ->expects(self::once())
            ->method('hydrateReviews')
            ->with([$firstProduct, $secondProduct])
        ;

        self::assertSame(
            $products,
            $this->collectionProvider->provide($operation, ['uriVariable' => 'value'], ['context' => 'value']),
        );
    }

    public function testDoesNotHydrateReviewsWhenAllOfThemAreAlreadyLoaded(): void
    {
        $operation = new GetCollection(class: Product::class);
        $products = [
            $this->createProductWithReviews($this->createLazyCollection(initialized: true)),
            $this->createProductWithReviews(new ArrayCollection()),
        ];

        $this->decoratedCollectionProvider->method('provide')->willReturn($products);
        $this->productRepository->expects(self::never())->method('hydrateReviews');

        self::assertSame($products, $this->collectionProvider->provide($operation));
    }

    public function testDoesNotHydrateReviewsWhenNoProductsAreProvided(): void
    {
        $operation = new GetCollection(class: Product::class);

        $this->decoratedCollectionProvider->method('provide')->willReturn([]);
        $this->productRepository->expects(self::never())->method('hydrateReviews');

        self::assertSame([], $this->collectionProvider->provide($operation));
    }

    public function testThrowsAnExceptionWhenOperationClassIsNotProduct(): void
    {
        $this->decoratedCollectionProvider->expects(self::never())->method('provide');

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide(new GetCollection(class: \stdClass::class));
    }

    public function testThrowsAnExceptionWhenOperationIsNotGetCollection(): void
    {
        $this->decoratedCollectionProvider->expects(self::never())->method('provide');

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide(new Get(class: Product::class));
    }

    private function createProductWithReviews(object $reviews): MockObject&ProductInterface
    {
        $product = $this->createMock(ProductInterface::class);
        $product->method('getReviews')->willReturn($reviews);

        return $product;
    }

    private function createLazyCollection(bool $initialized): AbstractLazyCollection&MockObject
    {
        $collection = $this->createMock(AbstractLazyCollection::class);
        $collection->method('isInitialized')->willReturn($initialized);

        return $collection;
    }
}
