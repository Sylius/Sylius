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

namespace Tests\Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\ProductAttributeValue;

use ApiPlatform\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\ProductAttributeValue\CollectionProvider;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Product\Model\ProductAttributeValue;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class CollectionProviderTest extends TestCase
{
    private MockObject&SectionProviderInterface $sectionProvider;

    private MockObject&ProductAttributeValueRepositoryInterface $attributeValueRepository;

    private LocaleContextInterface&MockObject $localeContext;

    private LocaleProviderInterface&MockObject $localeProvider;

    private MockObject&QueryResultCollectionExtensionInterface $extension;

    private CollectionProvider $collectionProvider;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->attributeValueRepository = $this->createMock(ProductAttributeValueRepositoryInterface::class);
        $this->localeContext = $this->createMock(LocaleContextInterface::class);
        $this->localeProvider = $this->createMock(LocaleProviderInterface::class);
        $this->extension = $this->createMock(QueryResultCollectionExtensionInterface::class);
        $defaultLocaleCode = 'en_US';
        $collectionExtensions = [$this->extension];
        $this->collectionProvider = new CollectionProvider(
            $collectionExtensions,
            $this->sectionProvider,
            $this->attributeValueRepository,
            $this->localeContext,
            $this->localeProvider,
            $defaultLocaleCode,
        );
    }

    public function testImplementsProviderInterface(): void
    {
        self::assertInstanceOf(ProviderInterface::class, $this->collectionProvider);
    }

    public function testProvidesProductAttributes(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);

        $operation = new GetCollection(class: ProductAttributeValueInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->localeContext->expects(self::once())->method('getLocaleCode')->willReturn('en_US');

        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en_US');

        $this->attributeValueRepository->expects(self::once())
            ->method('createByProductCodeAndLocaleQueryBuilder')
            ->with('PRODUCT_CODE', 'en_US', 'en_US', 'en_US')
            ->willReturn($queryBuilderMock);

        $this->extension->expects(self::once())
            ->method('applyToCollection')
            ->with($queryBuilderMock, new QueryNameGenerator(), ProductAttributeValueInterface::class, $operation, []);

        $this->extension->expects(self::once())
            ->method('supportsResult')
            ->with(ProductAttributeValueInterface::class, $operation)
            ->willReturn(true);

        $this->extension->expects(self::once())
            ->method('getResult')
            ->with($queryBuilderMock)
            ->willReturn(new \ArrayIterator([new ProductAttributeValue()]));

        self::assertInstanceOf(
            \ArrayIterator::class,
            $this->collectionProvider->provide($operation, ['code' => 'PRODUCT_CODE'], []),
        );
    }

    public function testReturnsQueryResultWhenNoExtensionsSupportResult(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var Query|MockObject $queryMock */
        $queryMock = $this->createMock(Query::class);

        $operation = new GetCollection(class: ProductAttributeValueInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());

        $this->localeContext->expects(self::once())->method('getLocaleCode')->willReturn('en_US');

        $this->localeProvider->expects(self::once())->method('getDefaultLocaleCode')->willReturn('en_US');

        $this->attributeValueRepository->expects(self::once())
            ->method('createByProductCodeAndLocaleQueryBuilder')
            ->with('PRODUCT_CODE', 'en_US', 'en_US', 'en_US')
            ->willReturn($queryBuilderMock);

        $this->extension->expects(self::once())
            ->method('applyToCollection')
            ->with($queryBuilderMock, new QueryNameGenerator(), ProductAttributeValueInterface::class, $operation, []);

        $this->extension->expects(self::once())
            ->method('supportsResult')
            ->with(ProductAttributeValueInterface::class, $operation)
            ->willReturn(false);

        $queryBuilderMock->expects(self::once())->method('getQuery')->willReturn($queryMock);

        $queryMock->expects(self::once())
            ->method('getResult')
            ->willReturn([$productAttributeValue = new ProductAttributeValue()]);

        self::assertSame(
            [$productAttributeValue],
            $this->collectionProvider->provide($operation, ['code' => 'PRODUCT_CODE'], []),
        );
    }

    public function testThrowsAnExceptionWhenOperationClassIsNotProductAttributeValue(): void
    {
        /** @var Operation|MockObject $operationMock */
        $operationMock = $this->createMock(Operation::class);

        $operationMock->expects(self::once())->method('getClass')->willReturn(\stdClass::class);

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionWhenOperationIsNotGetCollection(): void
    {
        $operationMock = $this->createMock(Operation::class);

        $operationMock
            ->expects(self::once())
            ->method('getClass')
            ->willReturn(ProductAttributeValueInterface::class);

        $this->sectionProvider
            ->method('getSection')
            ->willReturn(new ShopApiSection());

        self::expectException(\InvalidArgumentException::class);

        $this->collectionProvider->provide($operationMock);
    }

    public function testThrowsAnExceptionWhenOperationIsNotInShopApiSection(): void
    {
        $operation = new GetCollection(class: ProductAttributeValueInterface::class);

        $this->sectionProvider->expects(self::once())->method('getSection')->willReturn(new AdminApiSection());

        self::expectException(InvalidArgumentException::class);

        $this->collectionProvider->provide($operation);
    }
}
