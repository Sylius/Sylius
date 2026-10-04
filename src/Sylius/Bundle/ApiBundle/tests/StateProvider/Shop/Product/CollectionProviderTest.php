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
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\CollectionProvider;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductInterface;

final class CollectionProviderTest extends TestCase
{
    private MockObject&ProviderInterface $decoratedCollectionProvider;

    private ManagerRegistry&MockObject $managerRegistry;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->decoratedCollectionProvider = $this->createMock(ProviderInterface::class);
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->collectionProvider = new CollectionProvider(
            $this->decoratedCollectionProvider,
            $this->managerRegistry,
        );
    }

    public function testLoadsUninitializedReviewsOfAllProvidedProductsWithASingleQuery(): void
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

        $query = $this->createMock(Query::class);
        $query->expects(self::once())->method('getResult')->willReturn([]);

        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryBuilder->expects(self::once())->method('select')->with('product', 'review')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('from')->with(Product::class, 'product')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('leftJoin')->with('product.reviews', 'review')->willReturnSelf();
        $queryBuilder->expects(self::once())->method('andWhere')->with('product IN (:products)')->willReturnSelf();
        $queryBuilder
            ->expects(self::once())
            ->method('setParameter')
            ->with('products', [$firstProduct, $secondProduct])
            ->willReturnSelf()
        ;
        $queryBuilder->expects(self::once())->method('getQuery')->willReturn($query);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('createQueryBuilder')->willReturn($queryBuilder);

        $this->managerRegistry
            ->expects(self::once())
            ->method('getManagerForClass')
            ->with(Product::class)
            ->willReturn($entityManager)
        ;

        self::assertSame(
            $products,
            $this->collectionProvider->provide($operation, ['uriVariable' => 'value'], ['context' => 'value']),
        );
    }

    public function testDoesNotQueryForReviewsWhenAllOfThemAreAlreadyLoaded(): void
    {
        $operation = new GetCollection(class: Product::class);
        $products = [
            $this->createProductWithReviews($this->createLazyCollection(initialized: true)),
            $this->createProductWithReviews(new ArrayCollection()),
        ];

        $this->decoratedCollectionProvider->method('provide')->willReturn($products);
        $this->managerRegistry->expects(self::never())->method('getManagerForClass');

        self::assertSame($products, $this->collectionProvider->provide($operation));
    }

    public function testDoesNotQueryForReviewsWhenNoProductsAreProvided(): void
    {
        $operation = new GetCollection(class: Product::class);

        $this->decoratedCollectionProvider->method('provide')->willReturn([]);
        $this->managerRegistry->expects(self::never())->method('getManagerForClass');

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
