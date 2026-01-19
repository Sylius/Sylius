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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\RestrictingFilterEagerLoadingExtension;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Core\Model\ProductReview;

final class RestrictingFilterEagerLoadingExtensionTest extends TestCase
{
    /** @var QueryCollectionExtensionInterface|MockObject */
    private MockObject $decoratedExtensionMock;

    private RestrictingFilterEagerLoadingExtension $restrictingFilterEagerLoadingExtension;

    protected function setUp(): void
    {
        $this->decoratedExtensionMock = $this->createMock(QueryCollectionExtensionInterface::class);
        $this->restrictingFilterEagerLoadingExtension = new RestrictingFilterEagerLoadingExtension($this->decoratedExtensionMock, [
            Product::class => ['operations' => ['shop_get' => ['enabled' => true]]],
            ProductReview::class => ['operations' => ['shop_get' => ['enabled' => true], 'admin_get' => ['enabled' => false]]],
        ]);
    }

    public function testDoesNothingIfCurrentResourceAndOperationIsRestricted(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $args = [$queryBuilderMock, $queryNameGeneratorMock, Product::class, new Get(name: 'shop_get')];
        $this->decoratedExtensionMock->expects(self::never())->method('applyToCollection')->with(...$args);
        $this->restrictingFilterEagerLoadingExtension->applyToCollection(...$args);
    }

    public function testCallsFilterEagerLoadingExtensionIfCurrentResourceIsNotRestricted(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $args = [$queryBuilderMock, $queryNameGeneratorMock, Order::class, new Get(name: 'shop_get'), []];
        $this->decoratedExtensionMock->expects(self::once())->method('applyToCollection')->with(...$args);
        $this->restrictingFilterEagerLoadingExtension->applyToCollection(...$args);
    }

    public function testCallsFilterEagerLoadingExtensionIfCurrentOperationIsNotRestricted(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $args = [$queryBuilderMock, $queryNameGeneratorMock, Product::class, new Get(name: 'admin_get'), []];
        $this->decoratedExtensionMock->expects(self::once())->method('applyToCollection')->with(...$args);
        $this->restrictingFilterEagerLoadingExtension->applyToCollection(...$args);
    }

    public function testCallsFilterEagerLoadingExtensionIfCurrentResourceIsRestrictedButOperationIsNot(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $args = [$queryBuilderMock, $queryNameGeneratorMock, ProductReview::class, new Get(name: 'admin_get'), []];
        $this->decoratedExtensionMock->expects(self::once())->method('applyToCollection')->with(...$args);
        $this->restrictingFilterEagerLoadingExtension->applyToCollection(...$args);
    }
}
