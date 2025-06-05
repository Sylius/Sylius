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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductReview;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductReview\AcceptedExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionInterface;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Model\ReviewInterface;

final class AcceptedExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private AcceptedExtension $acceptedExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->acceptedExtension = new AcceptedExtension($this->sectionProviderMock);
    }

    public function testDoesNothingIfCurrentResourceIsNotAProductReview(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::never())->method('getSection');
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $queryBuilderMock->expects(self::never())->method('setParameter')->with($this->any());
        $this->acceptedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, stdClass::class);
    }

    public function testDoesNothingWhenTheOperationIsOutsideShopApiSection(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        /** @var SectionInterface|MockObject $sectionMock */
        $sectionMock = $this->createMock(SectionInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($sectionMock);
        $queryBuilderMock->expects(self::never())->method('getRootAliases');
        $queryBuilderMock->expects(self::never())->method('andWhere');
        $queryBuilderMock->expects(self::never())->method('setParameter')->with($this->any());
        $this->acceptedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ProductReview::class);
    }

    public function testFiltersAcceptedProductReviews(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn(new ShopApiSection());
        $queryNameGeneratorMock->expects(self::once())->method('generateParameterName')->with('status')->willReturn('status');
        $queryBuilderMock->expects(self::once())->method('getRootAliases')->willReturn(['o']);
        $queryBuilderMock->expects(self::once())->method('andWhere')->with('o.status = :status')->willReturn($queryBuilderMock);
        $queryBuilderMock->expects(self::once())->method('setParameter')->with('status', ReviewInterface::STATUS_ACCEPTED)->willReturn($queryBuilderMock);
        $this->acceptedExtension->applyToCollection($queryBuilderMock, $queryNameGeneratorMock, ProductReview::class);
    }
}
