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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use stdClass;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon\PostResultExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PostResultExtensionTest extends TestCase
{
    /** @var SectionProviderInterface|MockObject */
    private MockObject $sectionProviderMock;

    private PostResultExtension $postResultExtension;

    protected function setUp(): void
    {
        $this->sectionProviderMock = $this->createMock(SectionProviderInterface::class);
        $this->postResultExtension = new PostResultExtension($this->sectionProviderMock);
    }

    public function testQueryResultItemExtension(): void
    {
        $this->assertInstanceOf(PostResultExtension::class, $this->postResultExtension);
    }

    public function testAppliesNothingToItem(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        /** @var QueryNameGeneratorInterface|MockObject $queryNameGeneratorMock */
        $queryNameGeneratorMock = $this->createMock(QueryNameGeneratorInterface::class);
        $this->postResultExtension->applyToItem(
            $queryBuilderMock,
            $queryNameGeneratorMock,
            'resourceClass',
            ['identifiers'],
        );
    }

    public function testDoesNotSupportIfOperationIsNotPost(): void
    {
        self::assertFalse($this->postResultExtension->supportsResult(stdClass::class, null, []));
    }

    public function testDoesNotSupportIfSectionIsNotAdminApiSection(): void
    {
        /** @var ShopApiSection|MockObject $shopApiSectionMock */
        $shopApiSectionMock = $this->createMock(ShopApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($shopApiSectionMock);
        self::assertFalse($this->postResultExtension->supportsResult(stdClass::class, new Post(), []));
    }

    public function testDoesNotSupportIfResourceClassIsNotPromotionCouponInterface(): void
    {
        self::assertFalse($this->postResultExtension->supportsResult(stdClass::class, new Post(), []));
    }

    public function testSupportsResultIfOperationIsPostAndResourceClassIsPromotionCouponInterface(): void
    {
        /** @var AdminApiSection|MockObject $adminApiSectionMock */
        $adminApiSectionMock = $this->createMock(AdminApiSection::class);
        $this->sectionProviderMock->expects(self::once())->method('getSection')->willReturn($adminApiSectionMock);
        self::assertTrue($this->postResultExtension->supportsResult(PromotionCouponInterface::class, new Post(), []));
    }

    public function testReturnsNullResult(): void
    {
        /** @var QueryBuilder|MockObject $queryBuilderMock */
        $queryBuilderMock = $this->createMock(QueryBuilder::class);
        $this->assertNull($this->postResultExtension->getResult($queryBuilderMock, PromotionCouponInterface::class, new Post(), []));
    }
}
